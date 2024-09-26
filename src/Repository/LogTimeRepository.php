<?php

namespace App\Repository;

use App\Entity\LogTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogTime>
 *
 * @method LogTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogTime[]    findAll()
 * @method LogTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogTime::class);
    }

//    /**
//     * @return LogTime[] Returns an array of LogTime objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LogTime
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
