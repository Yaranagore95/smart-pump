<?php

namespace App\Repository;

use App\Entity\CuveMesure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CuveMesure|null find($id, $lockMode = null, $lockVersion = null)
 * @method CuveMesure|null findOneBy(array $criteria, array $orderBy = null)
 * @method CuveMesure[]    findAll()
 * @method CuveMesure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CuveMesureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CuveMesure::class);
    }

    // /**
    //  * @return CuveMesure[] Returns an array of CuveMesure objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CuveMesure
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
