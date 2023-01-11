<?php

namespace App\Repository;

use App\Entity\Chauffeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Chauffeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chauffeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chauffeur[]    findAll()
 * @method Chauffeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChauffeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chauffeur::class);
    }

    /**
     * @return Query
     */
    public function getChauffeurs(): Query
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();
    }


    public function getChauffeurByCriterer($info)
    {
        $info = '%' . trim(strtolower($info)) . '%';

        return $this->createQueryBuilder('c')
            ->where('TRIM(LOWER(c.nom)) LIKE :info')
            ->orWhere('TRIM(LOWER(c.prenom)) LIKE :info')
            ->orWhere('TRIM(LOWER(c.telephone)) LIKE :info')
            ->setParameter('info', $info)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
