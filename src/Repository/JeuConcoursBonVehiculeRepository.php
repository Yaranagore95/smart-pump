<?php

namespace App\Repository;

use App\Entity\JeuConcoursBonVehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JeuConcoursBonVehicule|null find($id, $lockMode = null, $lockVersion = null)
 * @method JeuConcoursBonVehicule|null findOneBy(array $criteria, array $orderBy = null)
 * @method JeuConcoursBonVehicule[]    findAll()
 * @method JeuConcoursBonVehicule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JeuConcoursBonVehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JeuConcoursBonVehicule::class);
    }

    public function getDistinctVehicules()
    {
        return $this->createQueryBuilder('j')
            ->select('j.matricule')
            ->distinct()
            ->orderBy('j.matricule', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getVehiculeByMatricule($matricule)
    {
        // return $t
    }

    /**
     * @param $matricule
     * @return float|int|mixed|string
     */
    public function getQteByMatricule($matricule)
    {
        return $this->createQueryBuilder('j')
            ->select('SUM(j.quantite) as somme, SUM(j.montant) as montant')
            ->where('j.matricule LIKE :matricule')
            ->setParameter('matricule', '%' . $matricule . '%')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return JeuConcoursBonVehicule[] Returns an array of JeuConcoursBonVehicule objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JeuConcoursBonVehicule
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
