<?php 
namespace App\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Core\Event\EntityAfterCreateEvent;
use App\Core\Event\EntityAfterUpdateEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @var array 
     */
    protected $snapshot = [];

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @inheritdoc
     * @param ManagerRegistry $registry
     * @param string $entityClass
     */
    public function __construct(
        ManagerRegistry $registry, 
        string $entityClass = ''
    ) {
        $allMetadata = $registry->getManager()->getMetadataFactory()->getAllMetadata();
        foreach ($allMetadata as $metadata) {
            if ($metadata->customRepositoryClassName === get_class($this)) {
                $entityClass = $metadata->getName();
                break;
            }
        }
        parent::__construct($registry, $entityClass);
    }

    /**
     * @required
     * @param ParameterBagInterface $parameterBag
     * @return $this 
     */
    public function setParameterBag(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        return $this;
    }

    /**
     * @required
     * @param Filesystem $filesystem
     * @return $this 
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        return $this;   
    }

    /**
     * @required 
     * @param EventDispatcherInterface $eventDispatcher
     * @return $this 
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    /**
     * Create a new entity
     * @return mixed
     */
    public function newEntity()
    {
        return new $this->_entityName();
    }

    /**
     * Copy values from array into entity
     * 
     * @param mixed $entity 
     * @param array $values
     * @return mixed
     */
    public function copyValues($entity, $values = [])
    {
        $metadata = $this->getClassMetadata();

        // fields 
        foreach ($values as $name => $value) {
            if ($name == 'id') {
                continue;
            }
            $setMethod = 'set' . ucfirst($name);
            try {
                $field = $metadata->getFieldMapping($name);
                if (method_exists($entity, $setMethod)) {
                    if (is_array($value) && count($value) > 0) { // options 
                        if (isset($value['id'])) { //single option
                            $value = $value['id'];
                        } elseif (isset($value[0]['id'])) { // multiple option
                            $value = implode(',', array_column($value, 'id'));
                        }
                    }
                    if ($field['type'] === 'integer') {
                        $value = intval($value);
                    } elseif ($field['type'] === 'datetime') {
                        if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $value) === 1) {
                            $value = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                        } elseif (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $value) === 1) {
                            $value = \DateTime::createFromFormat('Y-m-d', $value);
                        } else {
                            $value = null;
                        }
                    }

                    if (is_string($value) && (strpos($value, 'data:image/')) === 0) { // image update
                        $asset = $this->assetPackages->getPackage('user_upload');
                        $path = trim($asset->getBasePath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR .  (new \DateTime())->format('Ymd') . DIRECTORY_SEPARATOR;
                        $aPath = $this->parameterBag->get('kernel.project_dir') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $path;
                        if (!$this->filesystem->exists($aPath)) {
                            $this->filesystem->mkdir($aPath);
                        }

                        list($type, $image64) = explode(',', $value);
                        $classname = explode('\\', $this->entityClass);
                        $classname = end($classname);
                        $filename = strtolower($classname) . '-' . $name . '-' .time();
                        if (strpos($type, 'png') !== false) {
                            $filename .= '.png';
                        } else if (strpos($type, 'jpg') !== false) {
                            $filename .= '.jpg';
                        } else if (strpos($type, 'jpeg') !== false) {
                            $filename .= '.jpg';
                        }

                        $aPath .= $filename;
                        $this->filesystem->dumpFile($aPath, base64_decode($image64));
                        $value = '/' . $path . $filename;
                    }       
                    $entity->{$setMethod}($value);
                }
            } catch (MappingException $e) {
                try {
                    // try get associate 
                    $associate = $metadata->getAssociationMapping($name);
                    if (method_exists($entity, $setMethod)) {          
                        if (is_array($value) && count($value) > 0) {
                            if (isset($value['id'])) { // many to one 
                                $entity->{$setMethod}($this->getEntityManager()->getReference($associate['targetEntity'], $value['id']));
                            } else if (isset($value[0]['id'])) { // many to many / one to many
                                if ($associate['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY) {
                                    $getMethod = 'get' . ucfirst($name);
                                    if (method_exists($entity, $getMethod)) {
                                        $newIds = array_column($value, 'id');
                                        $oldIds = $entity->{$getMethod}()->getKeys();
                                        $insertIds = array_diff($newIds, $oldIds);
                                        $deleteIds = array_diff($oldIds, $newIds);
                                        foreach ($deleteIds as $id) {
                                            $entity->{$getMethod}()->remove($id);
                                        }
                                        foreach ($insertIds as $id) {
                                            $target = $this->getEntityManager()->getReference($associate['targetEntity'], $id);
                                            $method = 'get' . ucfirst($associate['mappedBy']);
                                            if (method_exists($target, $method)) {
                                                $target->{$method}()->add($entity);
                                                $entity->{$getMethod}()->set($id, $target);
                                            }        
                                        }
                                    }

                                } elseif ($associate['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY) {
                                    $getMethod = 'get' . ucfirst($name);
                                    if (method_exists($entity, $getMethod)) {
                                        $value = array_combine(array_column($value, 'id'), $value);
                                        $newIds = array_keys($value);
                                        $oldIds = $entity->{$getMethod}()->getKeys();
                                        $insertIds = array_diff($newIds, $oldIds);
                                        $deleteIds = array_diff($oldIds, $newIds);
                                        $updateIds = array_intersect($oldIds, $newIds);
                                        $repository = $this->getEntityManager()->getRepository($associate['targetEntity']);
                                        foreach ($deleteIds as $id) {
                                            $entity->{$getMethod}()->remove($id);
                                        }
                                        foreach ($insertIds as $id) {
                                            $target = $repository->newEntity();
                                            $method = 'set' . ucfirst($associate['mappedBy']);
                                            if (method_exists($target, $method)) {
                                                $target = $repository->copyValues($target, $value[$id]);
                                                $target->{$method}($entity);
                                                $entity->{$getMethod}()->add($target);
                                            }        
                                        }
                                        foreach ($updateIds as $id) {
                                            $target = $entity->{$getMethod}()->get($id);
                                            if ($target) {
                                                $target = $repository->copyValues($target, $value[$id]);
                                            } 
                                        }
                                    }
                                }
                            } 
                        }        
                    }
                } catch (MappingException $e) {
                    // igore
                }
            }   
        }

        return $entity;
    }

    /**
     * Transform a entity to array depend on $fields 
     * 
     * @return array 
     */
    public function toArray($entity, $fields = [])
    {
        // id is alsway return in result 
        $result = [
            'id' => $entity->getId()
        ];

        $metadata = $this->getClassMetadata();
        
        foreach ($fields as $field) {
            try {
                if (strpos($field, '[id') !== false) { // field with option or associate
                    $nfield = str_replace('[id]', '', $field);
                    $mapping = $metadata->getFieldMapping($nfield);
                    $method = 'get' . ucfirst($nfield);
                    if (method_exists($entity, $method)) {
                        $getMethodOptions = 'get' . ucfirst($nfield) . 'Options';
                        if (method_exists($entity, $getMethodOptions)) {
                            $options = $entity->{$getMethodOptions}();
                            if (isset($options[$entity->{$method}()])) {
                                $result[$nfield] = [
                                    'id' => $entity->{$method}(), 
                                    'name' => $this->translator->trans($options[$entity->{$method}()])
                                ];
                            }
                        }
                    }
                } else {
                    $mapping = $metadata->getFieldMapping($field);
                    $method = 'get' . ucfirst($field);
                    if (method_exists($entity, $method)) {
                        if ($mapping['type'] === 'datetime') {
                            $result[$field] = $entity->{$method}() instanceof \DateTime ? $entity->{$method}()->format('Y-m-d H:i:s') : ''; 
                        } elseif ($mapping['type'] === 'integer') {
                            $result[$field] = (int)$entity->{$method}();
                        } else {
                            $result[$field] = $entity->{$method}();
                        }
                    }
                }
            } catch (MappingException $e)  {
                try {
                    // then try get associate
                    preg_match('/\[([a-zA-Z_;()]+?)\]/', $field, $matches);
                    if (count($matches) === 2) {
                        $nfield = str_replace($matches[0], '', $field);
                        $matches[1] = str_replace('(', '[', $matches[1]);
                        $matches[1] = str_replace(')', ']', $matches[1]);
                        $aFields = explode(';', $matches[1]);
                        foreach ($aFields as $k => $f) {
                            $aFields[$k] = str_replace('_', ';', $f);
                        }
                        $associate = $metadata->getAssociationMapping($nfield);
                        $method = 'get' . ucfirst($nfield);
                        if (method_exists($entity, $method)) {
                            if (in_array($associate['type'], [ClassMetadataInfo::ONE_TO_ONE, ClassMetadataInfo::MANY_TO_ONE])) {
                                if ($entity->{$method}()) {
                                    $result[$nfield] = $this->getEntityManager()
                                        ->getRepository($associate['targetEntity'])
                                        ->toArray($entity->{$method}(), $aFields);
                                } else {
                                    $result[$nfield] = $entity->{$method}();
                                }
                                
                            } elseif (in_array($associate['type'], [ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY])) {
                                foreach ($entity->{$method}() as $aEntity) {
                                    $result[$nfield][] = $this->getEntityManager()
                                        ->getRepository($associate['targetEntity'])
                                        ->toArray($aEntity, $aFields);
                                }
                            }
                        }
                    }
                } catch (MappingException $e) {
                    // ignore
                }
            }
        }

        return $result;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     * @return QueryBuilder
     */
    public function search(QueryBuilder $qb, array $criteria)
    {
        $alias = $qb->getRootAlias();
        
        $criteria['pageSize'] = isset($criteria['pageSize']) ? $criteria['pageSize'] : 10; 
        $criteria['pageNum'] = isset($criteria['pageNum']) ? $criteria['pageNum'] : 1; 

        $qb->setMaxResults((int)$criteria['pageSize']);
        $qb->setFirstResult((((int)$criteria['pageNum']) - 1) * $qb->getMaxResults());

        if (isset($criteria['orderby'])) {
            $orderby = explode(' ', $criteria['orderby']);
            if (!isset($orderby[1])) {
                $orderby[1] = 'ASC';
            }
            $qb->orderBy($alias . '.' .$orderby[0], $orderby[1]);
        }
        
        $metadata = $this->getClassMetadata();
        foreach ($criteria as $name => $value) {
            if (in_array($name, ['fields', 'pageSize', 'pageNum'])) {
                continue;
            }

            if (is_array($value)) { // option or associate
                try {
                    $mapping = $metadata->getFieldMapping($name);
                    if (isset($value['id']) && strlen($value['id']) > 0) {
                        $qb->andWhere("{$alias}.{$name} = :$alias".'_'."$name")
                            ->setParameter($alias.'_'.$name, $value['id']);
                    }
                } catch (MappingException $e) {
                    try {
                        $associate = $metadata->getAssociationMapping($name);
                        if (isset($value['id'])) { // only query exist associate
                            if (strlen($value['id']) > 0) {
                                $qb->andWhere("{$alias}.{$name} = :$alias".'_'."$name")
                                    ->setParameter($alias.'_'.$name, $value['id']);
                            }
                        } else { // query field of associate
                           // TODO
                            $aR = $this->getEntityManager()->getRepository($associate['targetEntity']);
                            $aQb = $aR->createQueryBuilder($name);
                            $aQb->andWhere("$alias.$name = $name.id");
                            $aQb = $aR->search($aQb, $value); 
                            $qb->andWhere($qb->expr()->exists($aQb->getDQL()));
                            foreach ($aQb->getParameters() as $aParameter) {
                                $qb->setParameter($aParameter->getName(), $aParameter->getValue(), $aParameter->getType());
                            }
                        }
                    } catch (MappingException $e) {
                        // continue
                    }
                }
            } else {
                if (strlen($value) === 0) {
                    continue;
                }
                try {
                    $mapping = $metadata->getFieldMapping($name);
                    if ($mapping['type'] === 'string') {
                        if ($value[0] === '=') {
                            $qb->andWhere("{$alias}.{$name} = :$alias".'_'."$name")
                                ->setParameter($alias.'_'.$name, substr($value, 1));
                        } else {
                            $qb->andWhere("{$alias}.{$name} LIKE :$alias".'_'."$name")
                                ->setParameter($alias.'_'.$name, "%{$value}%");
                        }        
                    } elseif ($mapping['type'] === 'text') {
                        $qb->andWhere("{$alias}.{$name} LIKE :$alias".'_'."$name")
                            ->setParameter($alias.'_'.$name, "%{$value}%");
                    } elseif ($mapping['type'] === 'datetime') {
                        if (strpos($value, '~') !== false) {
                            $range = explode('~', $value);
                            if (count($range) === 2) {
                                if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $range[0]) === 1) { // from
                                    $qb->andWhere("{$alias}.{$name} >= :$alias".'_'."$name"."from")
                                        ->setParameter("{$alias}_{$name}from", $range[0] . ' 00:00:00');
                                } elseif (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}-[0-9]{2}/', $value) === 1) { // from
                                    $qb->andWhere("{$alias}.{$name} >= :$alias".'_'."$name"."from")
                                        ->setParameter("{$alias}_{$name}from", $range[0]);
                                }
                                if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $range[1]) === 1) { // from
                                    $qb->andWhere("{$alias}.{$name} <= :$alias".'_'."$name"."to")
                                        ->setParameter("{$alias}_{$name}to", $range[1] . ' 23:59:59');
                                } elseif (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}-[0-9]{2}/', $value) === 1) { // to
                                    $qb->andWhere("{$alias}.{$name} <= :$alias".'_'."$name"."to")
                                        ->setParameter("{$alias}_{$name}to", $range[1]);
                                }
                            }
                        } else {
                            if (strlen($value) >= 2  && in_array($value[0] . $value[1], ['>=', '<=', '<>'])) {
                                if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $value) === 1) {
                                    $qb->andWhere("{$alias}.{$name} {$value[0]}{$value[1]} :$alias".'_'."$name")
                                        ->setParameter($alias.'_'.$name, substr($value, 2));
                                } elseif (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}-[0-9]{2}/', $value) === 1) {
                                    $qb->andWhere("{$alias}.{$name} {$value[0]}{$value[1]} :$alias".'_'."$name")
                                        ->setParameter($alias.'_'.$name, substr($value, 2));
                                }    
                            } elseif (in_array($value[0], ['<', '>'])) {
                                if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $value) === 1) {
                                    $qb->andWhere("{$alias}.{$name} {$value[0]} :$alias".'_'."$name")
                                        ->setParameter($alias.'_'.$name, substr($value, 1));
                                } elseif (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}-[0-9]{2}/', $value) === 1) {
                                    $qb->andWhere("{$alias}.{$name} {$value[0]} :$alias".'_'."$name")
                                        ->setParameter($alias.'_'.$name, substr($value, 1));
                                }
                            } else {
                                if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $value) === 1) {
                                    $qb->andWhere("{$alias}.{$name} LIKE :$alias".'_'."$name")
                                        ->setParameter($alias.'_'.$name, "{$value}%");
                                } elseif (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}-[0-9]{2}/', $value) === 1) {
                                    $qb->andWhere("{$alias}.{$name} = :$alias".'_'."$name")
                                        ->setParameter($alias.'_'.$name, $value);
                                }
                            }
                        }
                    } elseif ($mapping['type'] === 'integer' || $mapping['type'] === 'float' || $mapping['type'] === 'decimal') {

                        if (strlen($value) >= 2  && in_array($value[0] . $value[1], ['>=', '<=', '<>'])) {
                            $qb->andWhere("{$alias}.{$name} {$value[0]}{$value[1]} :$alias".'_'."$name")
                                ->setParameter($alias.'_'.$name, substr($value, 2));
                        } elseif (in_array($value[0], ['<', '>'])) {
                            $qb->andWhere("{$alias}.{$name} {$value[0]} :$alias".'_'."$name")
                                ->setParameter($alias.'_'.$name, substr($value, 1));
                        } elseif (count(explode('...', $value)) === 2) {
                            list($from, $to) = explode('...', $value);
                            $qb->andWhere("{$alias}.{$name} >= :$alias".'_'."$name"."from AND {$alias}.{$name} <= :$alias".'_'."$name"."to")
                                ->setParameter("{$alias}_{$name}from", $from)
                                ->setParameter("{$alias}_{$name}to", $to);
                        } else {
                            if (strpos($value, ',') !== false) { // in
                                $qb->andWhere($qb->expr()->in("{$alias}.{$name}", explode(',', $value)));
                            } elseif (strpos($value, '!') !== false) { // not in
                                $qb->andWhere($qb->expr()->notIn("{$alias}.{$name}", explode('!', $value)));
                            } else {
                                $qb->andWhere("{$alias}.{$name} = :$alias".'_'."$name")
                                    ->setParameter($alias.'_'.$name, $value);
                            }
                        }
                    }
                } catch (MappingException $e) {
                    // continue
                }
            }
        }

        return $qb;
    }

    /**
     * Snapshot an entity
     * @param $entity 
     */
    public function snapshot($entity)
    {
        if ($entity->getId()) {
            $this->snapshots[$entity->getId()] = clone $entity;
        }
    }

    /**
     * Save the entity
     * 
     * @param Entity $entity
     * @param integer $flush
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return $this
     */
    public function save($entity)
    {
        // handle entity event 
        if (!$entity->getId()) {
            $createEvent = new EntityAfterCreateEvent($entity);
        } else if (isset($this->snapshots[$entity->getId()])) {
            $updateEvent = new EntityAfterUpdateEvent($this->snapshots[$entity->getId()]);
        }

        $em = $this->getEntityManager();

        // handle created & updated date 
        if (method_exists($entity, 'setCreatedDate')) {
            if (!$entity->getId()) {
                $entity->setCreatedDate(new \DateTime());
            }
        }
        if (method_exists($entity, 'setUpdatedDate')) {
            $entity->setUpdatedDate(new \DateTime());
        }

        $em->persist($entity);

        $em->flush();
        if (isset($createEvent)) {
            $this->eventDispatcher->dispatch($createEvent, $createEvent->getName());
        }
        if (isset($updateEvent)) {
            $updateEvent->setEntity($entity);
            $this->eventDispatcher->dispatch($updateEvent, $updateEvent->getName());
        }

        return $this;
    }

    /**
     * Delete an entity
     * @return $this
     */
    public function delete($entity)
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
        return $this;
    }
}
