<?php

namespace App\Repository;

use App\Entity\VenteCuve;
use App\Entity\VentePistolet;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method VentePistolet|null find($id, $lockMode = null, $lockVersion = null)
 * @method VentePistolet|null findOneBy(array $criteria, array $orderBy = null)
 * @method VentePistolet[]    findAll()
 * @method VentePistolet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VentePistoletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VentePistolet::class);
    }

    /**
     * @param $dateInf
     * @param $dateSup
     * @param $typeCarburantId
     * @return int|mixed|string
     * @throws Exception
     */
    public function ventePistoletCarburantByDate($dateInf, $dateSup, $typeCarburantId)
    {
        $dateDeb = new DateTime($dateInf, new DateTimeZone('GMT'));
        $dateFin = new DateTime($dateSup, new DateTimeZone('GMT'));

        return $this->createQueryBuilder('v')
            ->leftJoin('v.pistolet', 'p')
            ->leftJoin('p.typeCarburant', 't')
            ->where('v.createdAt BETWEEN :dateDeb and :dateFin')
            ->andWhere('t.id = :val')
            ->setParameter('val', $typeCarburantId)
            ->setParameter('dateDeb', $dateDeb)
            ->setParameter('dateFin', $dateFin)
            ->orderBy('v.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $pistoletId
     * @return int|mixed|string
     * @throws Exception
     */
    public function getPistoletYesterDayVente($pistoletId)
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
            ->leftJoin('v.pistolet', 'pistolet')
            ->where('pistolet.id = :id')
            ->andWhere('v.createdAt BETWEEN :dateDeb and :dateFin')
            ->setParameter('id', $pistoletId)
            ->setParameter('dateDeb', $dateDeb)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getVentePistoletByTypeCarburantAfterDate($typeCarburantId, $date)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.pistolet', 'pistolet')
            ->leftJoin('pistolet.typeCarburant', 'typeCarburant')
            ->where('typeCarburant.id = :typeCarburantId')
            ->andWhere('v.createdAt >= :date')
            ->select('SUM(v.quantite) as QTE')
            ->setParameter('typeCarburantId', $typeCarburantId)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
