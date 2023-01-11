<?php

namespace App\Repository;

use App\Entity\RetourEnCuve;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method RetourEnCuve|null find($id, $lockMode = null, $lockVersion = null)
 * @method RetourEnCuve|null findOneBy(array $criteria, array $orderBy = null)
 * @method RetourEnCuve[]    findAll()
 * @method RetourEnCuve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetourEnCuveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RetourEnCuve::class);
    }

    /**
     * @param int $stationId
     * @return int|mixed|string
     */
    public function getStationRetourEnCuves(int $stationId)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.typeCarburant', 'typeCarburant')
            ->leftJoin('typeCarburant.station', 'station')
            ->where('station.id = :stationId')
            ->setParameter('stationId', $stationId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $dateInf
     * @param $dateSup
     * @param $type
     * @return int|mixed|string
     * @throws Exception
     */
    public function getRetourEnCuveByDate($dateInf, $dateSup, $type)
    {
        $dateDeb = new DateTime($dateInf, new DateTimeZone('GMT'));
        $dateFin = new DateTime($dateSup, new DateTimeZone('GMT'));

        return $this->createQueryBuilder('r')
            ->leftJoin('r.typeCarburant', 't')
            ->andWhere('t.id = :val')
            ->andWhere('r.createdAt BETWEEN :dateDeb and :dateFin')
            ->setParameter('val', $type)
            ->setParameter('dateDeb', $dateDeb)
            ->setParameter('dateFin', $dateFin)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
