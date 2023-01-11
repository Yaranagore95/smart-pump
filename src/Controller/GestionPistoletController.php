<?php

namespace App\Controller;

use App\Repository\IndexationRepository;
use App\Repository\PistoletRepository;
use App\Repository\VentePistoletRepository;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionPistoletController extends AbstractController
{

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/pistolets", name="gestion_pistolets.index", methods={"POST", "GET"})
     * @throws Exception
     */
    public function index(Request $request, PistoletRepository $pistoletRepository, IndexationRepository $indexationRepository, VentePistoletRepository $ventePistoletRepository): Response
    {
        $pompes = $this->getUser()->getStructureClient()->getStations()[0]->getPompes();
        $pistolets = $pistoletRepository->getPistoletByStation($this->getUser()->getStructureClient()->getStations()[0]->getId());
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();
        $indexationErrors = [];
        foreach ($pistolets as $pistolet) {
            if ($indexationRepository->getPistoletDayIndexation($pistolet->getId()) == null) {
                $indexationErrors[$pistolet->getPompe()->getNumero() . ' | ' . $pistolet->getNumero()] = $pistolet->getPompe()->getNumero() . ' | ' . $pistolet->getNumero() . ' n\'a pas été indexé aujourd\'hui';
            }
        }
        $dateInf = null;
        $dateSup = null;
        $isDateVente = false;
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['btnVenteByDate'] == '1') {
            $dateInf = new DateTime($request->get('dateInf'));
            $dateSup = new DateTime($request->get('dateSup'));
            $this->addFlash('success', 'Les ventes des pistolets entre ' . $dateInf->format('d-m-Y') . ' et ' . $dateSup->format('d-m-Y'));
            $isDateVente = true;
        }
        $lastVentePistolet = [];
        foreach ($typeCarburants as $typeCarburant) {
            $montant = 0;
            $qte = 0;
            $founded = false;
            foreach ($typeCarburant->getPistolets() as $pistolet) {
                if ($dateInf != null && $dateSup != null) {
                    $ventePistolets = $ventePistoletRepository->getPistoletVenteByDate($pistolet->getId(), $dateInf, $dateSup);
                } else $ventePistolets = $ventePistoletRepository->getPistoletYesterDayVente($pistolet->getId());
                foreach ($ventePistolets as $ventePistolet) {
                    if ($ventePistolet != null) {
                        $founded = true;
                        $montant = $montant + $ventePistolet->getMontant();
                        $qte = $qte + $ventePistolet->getQuantite();
                    }
                }
            }
            if ($founded) {
                $d = date('Y-m-d');
                $venteP = [];
                if ($dateInf != null && $dateSup != null) {
                    $venteP['date'] = $dateInf->format('d-m-Y') . ' ET ' . $dateSup->format('d-m-Y');
                } else {
                    $dt = DateTime::createFromFormat('Y-m-d', $d);
                    // TODO CHANGER -100 A -1 JOUR POUR AVOIR HIER ET ENLEVER POUR DATESUP
                    $dt->modify('- 1 days');
                    $date = $dt->format('d-m-Y');
                    $venteP['date'] = $date;
                }
                $venteP['type'] = $typeCarburant->getName();
                $venteP['qte'] = $qte;
                $venteP['montant'] = $montant;
                $lastVentePistolet[] = $venteP;
            }
        }


        return $this->render('user-client/gestion/pistolet/index.html.twig', [
            'pompes' => $pompes,
            'pistolets' => $pistolets,
            'indexationErrors' => $indexationErrors,
            'pistoletYesterDayVentes' => $lastVentePistolet,
            'isDateVente' => $isDateVente
        ]);
    }
}
