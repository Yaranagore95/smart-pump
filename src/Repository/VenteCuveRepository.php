<?php

namespace App\Repository;

use App\Entity\VenteCuve;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method VenteCuve|null find($id, $lockMode = null, $lockVersion = null)
 * @method VenteCuve|null findOneBy(array $criteria, array $orderBy = null)
 * @method VenteCuve[]    findAll()
 * @method VenteCuve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VenteCuveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VenteCuve::class);
    }

    /**
     * @param $cuveId
     * @return int|mixed|string|VenteCuve
     * @throws Exception
     */
    public function getCuveYesterDayVente($cuveId)
    {
        $date = date('Y-m-d');
        $dateD = DateTime::createFromFormat('Y-m-d', $date);
        $dateF = DateTime::createFromFormat('Y-m-d', $date);
        // TODO CHANGER -3 A -1 JOUR POUR AVOIR HIER
        $dateD->modify('- 1 days');

        $dateInf = $dateD->format('Y-m-d');
        $dateSup = $dateF->format('Y-m-d');

        $dateDeb = new DateTime($dateInf, new DateTimeZone('GMT'));
        $dateFin = new DateTime($dateSup, new DateTimeZone('GMT'));

        return $this->createQueryBuilder('v')
            ->leftJoin('v.cuve', 'cuve')
            ->where('cuve.id = :id')
            ->andWhere('v.createdAt BETWEEN :dateDeb and :dateFin')
            ->setParameter('id', $cuveId)
            ->setParameter('dateDeb', $dateDeb)
            ->setParameter('dateFin', $dateFin)
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTime $dateInf
     * @param DateTime $dateSup
     * @param $cuveId
     * @return int|mixed|string
     */
    public function getCuveVentesByDate(DateTime $dateInf, DateTime $dateSup, $cuveId)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.cuve', 'cuve')
            ->where('cuve.id = :id')
            ->andWhere('v.createdAt BETWEEN :dateDeb and :dateFin')
            ->setParameter('id', $cuveId)
            ->setParameter('dateDeb', $dateInf)
            ->setParameter('dateFin', $dateSup)
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getVenteCuveByTypeCarburantAfterDate($typeCarburantId, $date)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.cuve', 'cuve')
            ->leftJoin('cuve.typeCarburant', 'typeCarburant')
            ->where('typeCarburant.id = :typeCarburantId')
            ->andWhere('v.createdAt >= :date')
            ->select('SUM(v.quantite) as QTE')
            ->setParameter('typeCarburantId', $typeCarburantId)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
