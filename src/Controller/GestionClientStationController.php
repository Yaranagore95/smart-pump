<?php

namespace App\Controller;

use App\Entity\BonClient;
use App\Entity\ClientStation;
use App\Form\BonClientType;
use App\Repository\BonClientRepository;
use App\Repository\ClientStationRepository;
use App\Repository\TypeCarburantRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionClientStationController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/client-stations", name="gestion_client_stations.index")
     * @throws Exception
     */
    public function index(Request $request, BonClientRepository $bonClientRepository, PaginatorInterface $paginator, ClientStationRepository $clientStationRepository, TypeCarburantRepository $typeCarburantRepository): Response
    {
        $bon = new BonClient();
        $form = $this->createForm(BonClientType::class, $bon);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($bon);
                $createdAt = new DateTime($request->get('createdAt'), new DateTimeZone('GMT'));
                $createdAt->modify('+1 minutes');
                $bon->setCreatedAt($createdAt);
                $clientStation = $clientStationRepository->find($request->get('clientStation'));
                $typeCarburant = $typeCarburantRepository->find($request->get('typeCarburant'));
                $bon->setClientStation($clientStation);
                if (array_key_exists($typeCarburant->getName(), $clientStation->getPrixCarburant())) {
                    $bon->setMontant($bon->getQuantite() * $clientStation->getPrixCarburant()[$typeCarburant->getName()]);
                } else {
                    $bon->setMontant($bon->getQuantite() * $typeCarburant->getPrixLittre());
                }
                $bon->setTypeCarburant($typeCarburant);
                $bon->setIsPaid(false);
                $bon->setNumero('BON_' . strtoupper($typeCarburant->getName()) . '_' . $clientStation->getId() . '_' . (count($clientStation->getBonClients()) + 1));
                $em->flush();
                $this->addFlash('success', 'Le bon client a été enregistre');
                return $this->redirectToRoute('gestion_client_stations.index');
            }
        }

        return $this->render('user-client/gestion/client-station/index.html.twig', [
            'clients' => $this->getUser()->getStructureClient()->getStations()[0]->getClientStations(),
            'form' => $form->createView(),
            'bonClients' => $paginator->paginate($bonClientRepository->getBonClientsByStation($this->getUser()->getStructureClient()->getStations()[0]->getId()), $request->query->getInt('page', 1), 30),
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/client-stations/{id}/show", name="gestion_client_stations.show")
     * @param ClientStation $clientStation
     * @param BonClientRepository $bonClientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function show(ClientStation $clientStation, BonClientRepository $bonClientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $date1 = $request->get('dateInf');
            $date2 = $request->get('dateSup');
            if ($date1 >= $date2) {
                $this->addFlash('danger', 'Erreur de date pour les bons du client');
                return $this->redirectToRoute('gestion_client_stations.show', ['id' => $clientStation->getId()]);
            } else {
                $dateInf = new DateTime($date1, new DateTimeZone('GMT'));
                $dateSup = new DateTime($date2, new DateTimeZone('GMT'));
                $this->addFlash('success', 'Les bons du client entre ' . $date1 . ' et ' . $date2);
                $bons = $bonClientRepository->getBonClientByDate($dateInf, $dateSup, $clientStation->getId());
            }
        } else {
            if ($request->get('type') == 'paid') {
                $bons = $bonClientRepository->findBy(['clientStation' => $clientStation, 'isPaid' => true], ['createdAt' => 'DESC']);
                $this->addFlash('success', 'Les bons payés du client');
            } elseif ($request->get('type') == 'no-paid') {
                $this->addFlash('success', 'Les bons non payés du client');
                $bons = $bonClientRepository->findBy(['clientStation' => $clientStation, 'isPaid' => false], ['createdAt' => 'DESC']);
            } elseif ($request->get('type') == 'all') {
                $this->addFlash('success', 'Tous les bons du client');
                $bons = $bonClientRepository->findBy(['clientStation' => $clientStation], ['createdAt' => 'DESC']);
            } else {
                $this->addFlash('success', 'Tous les bons du client');
                $bons = $bonClientRepository->findBy(['clientStation' => $clientStation], ['createdAt' => 'DESC']);
            }
        }

        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();
        $arrayQteBon = $this->getBonQte($typeCarburants, $bons);
        return $this->render('user-client/gestion/client-station/show.html.twig', [
            'client' => $clientStation,
            'bonClients' => $paginator->paginate($bons, $request->query->getInt('page', 1), 10000000000000),
            'arrayQteBon' => $arrayQteBon
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/bons-client/{id}/paid", name="gestion_bons_client.paid")
     * @param BonClient $bonClient
     * @param BonClientRepository $bonClientRepository
     * @return RedirectResponse
     * @throws Exception
     */
    public function passBonToPaid(BonClient $bonClient, BonClientRepository $bonClientRepository): RedirectResponse
    {
        $bonClient->setIsPaid(true)
            ->setUpdatedAt(new DateTime('now', new DateTimeZone('GMT')));
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Le bon client est passé à payer !!!');
        return $this->redirectToRoute('gestion_client_stations.show', ['id' => $bonClient->getClientStation()->getId()]);
    }

    /**
     * * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/bons-client/{id}/not-paid", name="gestion_bons_client.notPaid")
     * @param BonClient $bonClient
     * @param BonClientRepository $bonClientRepository
     * @return RedirectResponse
     * @throws Exception
     */
    public function passBonToNotPaid(BonClient $bonClient, BonClientRepository $bonClientRepository): RedirectResponse
    {
        $bonClient->setIsPaid(false)
            ->setUpdatedAt(new DateTime('now', new DateTimeZone('GMT')));
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Le bon client est passé à non payer !!!');
        return $this->redirectToRoute('gestion_client_stations.show', ['id' => $bonClient->getClientStation()->getId()]);
    }

    public function getBonQte($typeCarburants, $bons): array
    {
        $arrayQteBon = [];
        foreach ($typeCarburants as $typeCarburant) {
            $type = $typeCarburant->getName();
            $qteBon = 0;
            foreach ($bons as $bon) {
                if ($bon->getTypeCarburant() == $typeCarburant && !$bon->getIsPaid()) {
                    $qteBon = $qteBon + $bon->getQuantite();
                }
            }
            $arrayQteBon[$type] = $qteBon;
        }

        return $arrayQteBon;
    }
}
