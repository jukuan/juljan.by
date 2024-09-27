<?php

namespace App\Repository;

use App\Entity\PostBe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostBe>
 *
 * @method PostBe|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostBe|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostBe[]    findAll()
 * @method PostBe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostBeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostBe::class);
    }

    //    /**
    //     * @return PostBe[] Returns an array of PostBe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PostBe
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
