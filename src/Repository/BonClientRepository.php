<?php

namespace App\Repository;

use App\Entity\BonClient;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method BonClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method BonClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method BonClient[]    findAll()
 * @method BonClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BonClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BonClient::class);
    }

    /**
     * @param $stationId
     * @return mixed
     */
    public function getBonClientsByStation($stationId)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.typeCarburant', 'typeCarburant')
            ->leftJoin('typeCarburant.station', 'station')
            ->where('station.id = :stationId')
            ->orderBy('b.createdAt', 'DESC')
            ->setParameter('stationId', $stationId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $dateInf
     * @param $dateSup
     * @param $type
     * @return int|mixed|string|BonClient[]
     * @throws Exception
     */
    public function getNotPaidBonClientsByDateByTypeCarburant($dateInf, $dateSup, $type)
    {
        $dateDeb = new DateTime($dateInf, new DateTimeZone('GMT'));
        $dateFin = new DateTime($dateSup, new DateTimeZone('GMT'));

        return $this->createQueryBuilder('b')
            ->leftJoin('b.typeCarburant', 't')
            ->andWhere('t.id = :val')
            ->andWhere('b.createdAt BETWEEN :dateDeb and :dateFin')
            // ->andWhere('b.isPaid = 0')
            ->setParameter('val', $type)
            ->setParameter('dateDeb', $dateDeb)
            ->setParameter('dateFin', $dateFin)
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws Exception
     */
    public function getBonClientByDate($dateInf, $dateSup, $clientId)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.typeCarburant', 'typeCarburant')
            ->leftJoin('b.clientStation', 'clientStation')
            ->andWhere('clientStation.id = :clientId')
            ->andWhere('b.createdAt BETWEEN :dateInf and :dateSup')
            ->setParameter('clientId', $clientId)
            ->setParameter('dateInf', $dateInf)
            ->setParameter('dateSup', $dateSup)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return BonClient[] Returns an array of BonClient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BonClient
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
