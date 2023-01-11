<?php

namespace App\Repository;

use App\Entity\DetailDepense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DetailDepense|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailDepense|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailDepense[]    findAll()
 * @method DetailDepense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailDepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailDepense::class);
    }

    // /**
    //  * @return DetailDepense[] Returns an array of DetailDepense objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DetailDepense
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
