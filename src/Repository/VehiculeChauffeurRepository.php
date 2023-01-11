<?php

namespace App\Repository;

use App\Entity\VehiculeChauffeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VehiculeChauffeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method VehiculeChauffeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method VehiculeChauffeur[]    findAll()
 * @method VehiculeChauffeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehiculeChauffeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehiculeChauffeur::class);
    }

    /**
     * @param $typeVehiculeId
     * @return int|mixed|string
     */
    public function getVehiculesByTypeVehiculeId($typeVehiculeId)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.chauffeur', 'chauffeur')
            ->leftJoin('chauffeur.typeVehicule', 'typeVehicule')
            ->where('typeVehicule.id = :typeVehiculeId')
            ->setParameter('typeVehiculeId', $typeVehiculeId)
            ->orderBy('v.createdAt')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $stationId
     * @return Query
     */
    public function getVehiculesByStationId($stationId): Query
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.typeCarburant', 'typeCarburant')
            ->leftJoin('typeCarburant.station', 'station')
            ->where('station.id = :stationId')
            ->setParameter('stationId', $stationId)
            ->orderBy('v.updatedAt', 'DESC')
            ->getQuery();
    }

    /**
     * @param $info
     * @return int|mixed|string
     */
    public function getVehiculesByCritere($info)
    {
        $info = '%' . trim(strtolower($info)) . '%';

        return $this->createQueryBuilder('v')
            ->where('TRIM(LOWER(v.immatriculation)) LIKE :info')
            ->setParameter('info', $info)
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
