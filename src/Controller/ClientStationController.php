<?php

namespace App\Controller;

use App\Entity\ClientStation;
use App\Form\ClientStationType;
use App\Repository\ClientStationRepository;
use App\Repository\UserRepository;
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

class ClientStationController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/clients", name="clients.clientStations")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        // TODO A VOIR POUR LES MUTIPLES STATIONS
        return $this->render('user-client/dashboard/client-station/index.html.twig', [
            'clients' => $paginator->paginate($this->getUser()->getStructureClient()->getStations()[0]->getClientStations(), $request->query->getInt('page', 1), 30)
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/clients/new", name="clients.clientStations.create", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function add(Request $request): Response
    {
        $client = new ClientStation();
        // TODO A CHANGER POUR MULTIPLE STATION
        $form = $this->createForm(ClientStationType::class, $client);
        $form->handleRequest($request);
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($client);
            $client->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $client->setStation($this->getUser()->getStructureClient()->getStations()[0]);
            $prixTypeCarburant = [];
            foreach ($typeCarburants as $typeCarburant) {
                if ($request->get('prixTypeCarburant' . $typeCarburant->getId()) !== '') {
                    $prix = $request->get('prixTypeCarburant' . $typeCarburant->getId());
                    $prixTypeCarburant += array($typeCarburant->getName() => $prix);
                }
            }
            $client->setPrixCarburant($prixTypeCarburant);
            $this->addFlash('success', 'Le client a été enregistré');
            $manager->flush();

            $manager->flush();
            return $this->redirectToRoute('clients.clientStations');
        }

        return $this->render('user-client/dashboard/client-station/add.html.twig', [
            'form' => $form->createView(),
            'typeCarburants' => $typeCarburants
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/clients/{id}/edit", name="clients.clientStations.edit", methods={"GET", "POST"})
     * @param Request $request
     * @param integer $id
     * @param ClientStationRepository $clientStationRepository
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, int $id, ClientStationRepository $clientStationRepository): Response
    {
        $client = $clientStationRepository->find($id);

        $form = $this->createForm(ClientStationType::class, $client);

        $form->handleRequest($request);
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();

            $client->setUpdatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $prixTypeCarburant = [];
            foreach ($typeCarburants as $typeCarburant) {
                if ($request->get('prixTypeCarburant' . $typeCarburant->getId()) !== '') {
                    $prix = $request->get('prixTypeCarburant' . $typeCarburant->getId());
                    $prixTypeCarburant += array($typeCarburant->getName() => $prix);
                }
            }
            $client->setPrixCarburant($prixTypeCarburant);
            $this->addFlash('success', 'Le client a été modifié');
            $manager->flush();

            return $this->redirectToRoute('clients.clientStations');
        }

        return $this->render('user-client/dashboard/client-station/edit.html.twig', [
            'form' => $form->createView(),
            'typeCarburants' => $typeCarburants,
            'client' => $client

        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/clients/{id}/delete", name="clients.clientStations.delete", methods={"DELETE"})
     * @param Request $request
     * @param integer $id
     * @param ClientStationRepository $clientRepository
     * @return RedirectResponse
     */
    public function delete(Request $request, int $id, ClientStationRepository $clientRepository): RedirectResponse
    {
        $client = $clientRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($client);
            $entityManager->flush();
            $this->addFlash('success', 'Le client a été suprimé');
        }
        return $this->redirectToRoute('clients.clientStations');
    }
}
