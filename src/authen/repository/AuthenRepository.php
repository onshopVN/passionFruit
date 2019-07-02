<?php

namespace App\authen\repository;

use App\authen\entity\Authen;
use App\authen\form\AuthenType;
use App\deputation\repository\AuthenRepositoryInterface;
use App\skeleton\repository\SkeletonRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method Authen|null find($id, $lockMode = null, $lockVersion = null)
 * @method Authen|null findOneBy(array $criteria, array $orderBy = null)
 * @method Authen[]    findAll()
 * @method Authen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthenRepository extends SkeletonRepository implements AuthenRepositoryInterface
{
    protected  $entityClass = Authen::class;
    protected  $formClass = AuthenType::class;
    private $passwordEncoder;

    /**
     * AuthenRepository constructor.
     * @param RegistryInterface $registry
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(RegistryInterface $registry, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct($registry);
    }

    public function queryBuilderWithObject($term): QueryBuilder
    {

        $qb = $this->createQueryBuilder('a')
//            ->innerJoin('c.article', 'a')
            ->addSelect('a');
        if ($term->getUsername()) {
            $name  = $term->getUsername();
//            $qb->orWhere('a.username LIKE :username')
//                ->setParameter('username', '%'.$name.'%')
            $qb->orWhere('a.email_en LIKE :email')
                ->setParameter('email', '%'.$name.'%')
            ;
        }
        return $qb->orderBy('a.email', 'DESC');
    }

    /**
     * @param Entity $object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save($object)
    {
        $em = $this->getEntityManager();
        $object->setPassword(
            $this->passwordEncoder->encodePassword(
            $object,
            $object->getPassword()
            ));
        $em->persist($object);
        $em->flush($object);
    }
}
