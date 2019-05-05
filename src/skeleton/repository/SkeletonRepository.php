<?php

namespace App\skeleton\repository;

use App\deputation\repository\RepositoryInterface;
use App\skeleton\entity\AbstractEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @method Entity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entity[]    findAll()
 * @method Entity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkeletonRepository extends ServiceEntityRepository implements RepositoryInterface
{
    protected $formClass;
    protected $entityClass;
    protected $session;

    /**
     * SkeletonRepositoryInterface constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        if ($this->entityClass)
        {
            parent::__construct($registry, $this->entityClass);
        }
    }

    /**
     * @return string|null
     */
    public function getFormClass(): ?string
    {
        return $this->formClass;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function setSession(SessionInterface $session)
    {
        $this->session= $session;
        return $this;
    }

    /**
     * @param string|null $formClass
     * @return $this
     */
    public function setFormClass(?string $formClass)
    {
        $this->formClass = $formClass;
        return $this;
    }

    /**
     * @param Entity $object
     * @return QueryBuilder
     */
    public function queryBuilderWithObject($object): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');
//        $qb->innerJoin('c.article', 'a');
//        $qb->addSelect('a');
//        $qb->andWhere('a.name LIKE :term')
//        $qb->setParameter('term', '%'.$anme.'%');
//        $qb->orderBy('a.name', 'DESC');
        return $qb;
    }

    /**
     * @return mixed
     */
    public function newEntity()
    {
        $_entityName = $this->_entityName;
        return new $_entityName();
    }

    /**
     * @param Entity $object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($object)
    {
        $em = $this->getEntityManager();
        $em->persist($object);
        $em->flush($object);
    }

    /**
     * @param Entity $object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete($object)
    {
        $em = $this->getEntityManager();
        $em->remove($object);
        $em->flush($object);
    }
}