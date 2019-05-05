<?php

namespace App\profile\repository;

use App\deputation\repository\ProfileRepositoryInterface;
use App\skeleton\repository\SkeletonRepository;
use App\profile\entity\Profile;
use App\profile\form\ProfileType;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile[]    findAll()
 * @method Profile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileRepository extends SkeletonRepository implements ProfileRepositoryInterface
{
    protected  $entityClass = Profile::class;
    protected  $formClass = ProfileType::class;

    //https://symfonycasts.com/screencast/doctrine-relations/pagination

    /**
     * @return QueryBuilder
     */
    public function queryBuilderWithObject($term): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
//            ->innerJoin('c.article', 'a')
            ->addSelect('a');
        if ($term) {
            $name  = $term->getName();
            $qb->andWhere('a.name LIKE :term')
                ->setParameter('term', '%'.$name.'%')
            ;
        }
        return $qb->orderBy('a.name', 'DESC');
    }

}