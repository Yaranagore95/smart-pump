<?php

namespace App\Controller;

use App\Entity\customEntity\VenteGlobalTypeCarburant;
use App\Repository\BonClientRepository;
use App\Repository\DepenseRepository;
use App\Repository\RetourEnCuveRepository;
use App\Repository\VentePistoletRepository;
use DateTime;
use DateTimeZone;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionEtatVenteController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/etats", name="gestion_etats.index", methods={"GET", "POST"})
     * @param Request $request
     * @param BonClientRepository $bonClientRepository
     * @param DepenseRepository $depenseJournalierRepository
     * @param VentePistoletRepository $ventePistoletCarburantRepository
     * @param RetourEnCuveRepository $retourEnCuveRepository
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, BonClientRepository $bonClientRepository, DepenseRepository $depenseJournalierRepository, VentePistoletRepository $ventePistoletCarburantRepository, RetourEnCuveRepository $retourEnCuveRepository): Response
    {
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dateInf = $request->get('date_inf');
            $dateSup = $request->get('date_sup');
            $dateD = DateTime::createFromFormat('Y-m-d', $dateInf);
            $dateF = DateTime::createFromFormat('Y-m-d', $dateSup);
        } else {
            $date = date('Y-m-d');
            $dateD = DateTime::createFromFormat('Y-m-d', $date);
            $dateF = DateTime::createFromFormat('Y-m-d', $date);
            $dateD->modify('- 1 days');
        }
        $dateInf = $dateD->format('Y-m-d');
        $dateSup = $dateF->format('Y-m-d');
        $this->addFlash('success', 'Les ventes de ' . $dateInf . ' - ' . $dateSup);
        // DEBUT AVEC ARRAY
        $etatJournalier = array();

        foreach ($typeCarburants as $typeCarburant) {
            $venteByTypeCarburant = $ventePistoletCarburantRepository->ventePistoletCarburantByDate($dateInf, $dateSup, $typeCarburant);
            $qte = 0;
            $montant = 0;
            foreach ($venteByTypeCarburant as $vente) {
                $qte = $qte + $vente->getQuantite();
                $montant = $montant + $vente->getMontant();
            }
            $bons = $bonClientRepository->getNotPaidBonClientsByDateByTypeCarburant($dateInf, $dateSup, $typeCarburant->getId());
            $qteBon = 0;
            $montantBon = 0;
            foreach ($bons as $bon) {
                $qteBon = $qteBon + $bon->getQuantite();
                $montantBon = $montantBon + $bon->getMontant();
            }
            $retourEnCuves = $retourEnCuveRepository->getRetourEnCuveByDate($dateInf, $dateSup, $typeCarburant->getId());
            $qteRetour = 0;
            if ($retourEnCuves != []) {
                $qteRetour = 0;
                foreach ($retourEnCuves as $retourEnCuve) {
                    $qteRetour = $qteRetour + $retourEnCuve->getQuantite();
                }
            }

            $etatJournalier[$typeCarburant->getName()] = array(
                'quantite' => $qte,
                'montant' => $qte * $montant,
                'qteBon' => $qteBon,
                'montantBon' => $montantBon,
                'montantNet' => $montant - $montantBon,
                'retourEnCuve' => $qteRetour
            );
        }
        // FIN ARRAY


        // DEBUT
        $arrayVentePistoletTypeCarburant = [];
        $arrayRetourEnCuve = [];

        foreach ($typeCarburants as $typeCarburant) {
            $ventePistoletTypeCarburants = $ventePistoletCarburantRepository->ventePistoletCarburantByDate($dateInf, $dateSup, $typeCarburant->getId());
            $venteGlobalTypeCarburant = new VenteGlobalTypeCarburant();
            $venteGlobalTypeCarburant->setTypeCarburant($typeCarburant);
            $qte = 0;
            $montant = 0;
            foreach ($ventePistoletTypeCarburants as $ventePistoletTypeCarburant) {
                $qte = $qte + $ventePistoletTypeCarburant->getQuantite();
                $montant = $montant + $ventePistoletTypeCarburant->getMontant();
            }
            $venteGlobalTypeCarburant->setQte($qte);
            $venteGlobalTypeCarburant->setMontant($montant);
            $bons = $bonClientRepository->getNotPaidBonClientsByDateByTypeCarburant($dateInf, $dateSup, $typeCarburant->getId());
            $qteBon = 0;
            $montantBon = 0;
            foreach ($bons as $bon) {
                $qteBon = $qteBon + $bon->getQuantite();
                $montantBon = $montantBon + $bon->getMontant();
            }
            $venteGlobalTypeCarburant->setQteBon($qteBon);
            $venteGlobalTypeCarburant->setMontantBon($montantBon);
            $venteGlobalTypeCarburant->setMontantNet($montant - $montantBon);

            $arrayVentePistoletTypeCarburant[] = $venteGlobalTypeCarburant;

            $retourEnCuves = $retourEnCuveRepository->getRetourEnCuveByDate($dateInf, $dateSup, $typeCarburant->getId());

            if ($retourEnCuves != []) {
                foreach ($retourEnCuves as $retourEnCuve) {
                    $arrayRetourEnCuve[] = $retourEnCuve;
                }
            }
        }

        $montantGlobal = 0;
        foreach ($arrayVentePistoletTypeCarburant as $venteGlobalTypeCarburant) {
            $montantGlobal = $montantGlobal + $venteGlobalTypeCarburant->getMontantNet();
        }

        $depenses = $depenseJournalierRepository->depensesJournalierByDate($dateInf, $dateSup, $this->getUser()->getStructureClient()->getStations()[0]->getId());

        $montantDepenses = 0;

        foreach ($depenses as $depense) {
            $montantDepenses = $montantDepenses + $depense->getMontant();
        }

        $qteRetour = 0;
        $montantRetourEnCuve = 0;

        foreach ($arrayRetourEnCuve as $retourEnCuve) {
            $qteRetour = $qteRetour + $retourEnCuve->getQuantite();
            $montantRetourEnCuve = $montantRetourEnCuve + $retourEnCuve->getQuantite() * $retourEnCuve->getTypeCarburant()->getPrixLittre();
        }

        $montantNet = $montantGlobal - $montantDepenses - $montantRetourEnCuve;

        return $this->render('user-client/gestion/etat/index.html.twig', [
            'depenses' => $depenses,
            'retourCuves' => $arrayRetourEnCuve,
            'montantNet' => $montantNet,
            'dateInf' => $dateInf,
            'dateSup' => $dateSup,
            'montantDepense' => $montantDepenses,
            'arrayVentePistoletTypeCarburant' => $arrayVentePistoletTypeCarburant
        ]);

        // FIN
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/etats/generate-pdf/{dateInf}/and/{dateSup}/vente", name="gestion_etats.generatePDF", methods={"GET"})
     * @param VentePistoletRepository $ventePistoletCarburantRepository
     * @param $dateInf
     * @param $dateSup
     * @param BonClientRepository $bonClientRepository
     * @param DepenseRepository $depenseJournalierRepository
     * @param RetourEnCuveRepository $retourEnCuveRepository
     * @return Response
     * @throws Exception
     */
    public function generatePDF(VentePistoletRepository $ventePistoletCarburantRepository, $dateInf, $dateSup, BonClientRepository $bonClientRepository, DepenseRepository $depenseJournalierRepository, RetourEnCuveRepository $retourEnCuveRepository): Response
    {

        // DEBUT

        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();
        $arrayVentePistoletTypeCarburant = [];
        $arrayRetourEnCuve = [];

        foreach ($typeCarburants as $typeCarburant) {
            $ventePistoletTypeCarburants = $ventePistoletCarburantRepository->ventePistoletCarburantByDate($dateInf, $dateSup, $typeCarburant->getId());

            $venteGlobalTypeCarburant = new VenteGlobalTypeCarburant();

            $venteGlobalTypeCarburant->setTypeCarburant($typeCarburant);

            $qte = 0;
            $montant = 0;
            foreach ($ventePistoletTypeCarburants as $ventePistoletTypeCarburant) {
                $qte = $qte + $ventePistoletTypeCarburant->getQuantite();
                $montant = $montant + $ventePistoletTypeCarburant->getMontant();
            }
            $venteGlobalTypeCarburant->setQte($qte);
            $venteGlobalTypeCarburant->setMontant($montant);

            $bons = $bonClientRepository->getNotPaidBonClientsByDateByTypeCarburant($dateInf, $dateSup, $typeCarburant->getId());

            $qteBon = 0;
            $montantBon = 0;

            foreach ($bons as $bon) {
                $qteBon = $qteBon + $bon->getQuantite();
                $montantBon = $montantBon + $bon->getMontant();
            }
            $venteGlobalTypeCarburant->setQteBon($qteBon);
            $venteGlobalTypeCarburant->setMontantBon($montantBon);
            $venteGlobalTypeCarburant->setMontantNet($montant - $montantBon);

            $arrayVentePistoletTypeCarburant[] = $venteGlobalTypeCarburant;

            $retourEnCuves = $retourEnCuveRepository->getRetourEnCuveByDate($dateInf, $dateSup, $typeCarburant->getId());

            if ($retourEnCuves != []) {
                foreach ($retourEnCuves as $retourEnCuve) {
                    $arrayRetourEnCuve[] = $retourEnCuve;
                }
            }
        }

        $montantGlobal = 0;
        foreach ($arrayVentePistoletTypeCarburant as $venteGlobalTypeCarburant) {
            $montantGlobal = $montantGlobal + $venteGlobalTypeCarburant->getMontantNet();
        }

        $depenses = $depenseJournalierRepository->depensesJournalierByDate($dateInf, $dateSup, $this->getUser()->getId());

        $montantDepenses = 0;

        foreach ($depenses as $depense) {
            $montantDepenses = $montantDepenses + $depense->getMontant();
        }

        $qteRetour = 0;
        $montantRetourEnCuve = 0;

        foreach ($arrayRetourEnCuve as $retourEnCuve) {
            $qteRetour = $qteRetour + $retourEnCuve->getQuantite();
            $montantRetourEnCuve = $montantRetourEnCuve + $retourEnCuve->getQuantite() * $retourEnCuve->getTypeCarburant()->getPrixLittre();
        }

        $montantNet = $montantGlobal - $montantDepenses - $montantRetourEnCuve;

        // FIN

        $html = $this->renderView('document-generer/mypdf.html.twig', [
            'depenses' => $depenses,
            'montantNet' => $montantNet,
            'dateInf' => $dateInf,
            'dateSup' => $dateSup,
            'montantDepense' => $montantDepenses,
            'arrayVentePistoletTypeCarburant' => $arrayVentePistoletTypeCarburant,
            'createdAt' => new DateTime('now', new DateTimeZone('GMT'))
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('tempDir', '/home2/directory/public_html/directory/pdf-export/tmp');
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4');

        $dompdf->render();

        $dompdf->stream('vente-' . $dateInf . '-' . $dateSup . "-doc.pdf", [
            "Attachment" => true
        ]);

        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
