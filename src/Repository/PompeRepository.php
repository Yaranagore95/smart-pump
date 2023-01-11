<?php

namespace App\Repository;

use App\Entity\Pompe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pompe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pompe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pompe[]    findAll()
 * @method Pompe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PompeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pompe::class);
    }

    /**
     * @param int $structureClientId
     * @param $numero
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function getPompeByClientStructureAndNumero(int $structureClientId, $numero)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.station', 'station')
            ->leftJoin('station.structureClient', 'structureClient')
            ->where('structureClient.id = :structureClient')
            ->andWhere('p.numero = :numero')
            ->setParameter('structureClient', $structureClientId)
            ->setParameter('numero', $numero)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Pompe[] Returns an array of Pompe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pompe
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
