<?php

namespace App\Repository;

use App\Entity\BonChauffeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BonChauffeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method BonChauffeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method BonChauffeur[]    findAll()
 * @method BonChauffeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BonChauffeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BonChauffeur::class);
    }

    public function getBonChaffeursByStationId($stationId)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.vehiculeChauffeur', 'vehiculeChauffeur')
            ->leftJoin('vehiculeChauffeur.typeCarburant', 'typeCarburant')
            ->leftJoin('typeCarburant.station', 'station')
            ->where('station.id = :stationId')
            ->setParameter('stationId', $stationId)
            ->orderBy('b.updatedAt', 'DESC')
            ->getQuery();
            // ->getResult();
    }

    /**
     * @param $typeVehiculeId
     * @return int|mixed|string
     */
    public function getBonChauffeursByTypeVehiculeId($typeVehiculeId)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.vehiculeChauffeur', 'vehiculeChauffeur')
            ->leftJoin('vehiculeChauffeur.chauffeur', 'chauffeur')
            ->leftJoin('chauffeur.typeVehicule', 'typeVehicule')
            ->where('typeVehicule.id = :typeVehiculeId')
            ->setParameter('typeVehiculeId', $typeVehiculeId)
            ->orderBy('b.createdAt')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $chauffeurId
     * @return int|mixed|string
     */
    public function getBonsByChauffeurId($chauffeurId)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.vehiculeChauffeur', 'vehiculeChauffeur')
            ->leftJoin('vehiculeChauffeur.chauffeur', 'chauffeur')
            ->where('chauffeur.id = :chauffeurId')
            ->setParameter('chauffeurId', $chauffeurId)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return BonChauffeur[] Returns an array of BonChauffeur objects
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
    public function findOneBySomeField($value): ?BonChauffeur
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
