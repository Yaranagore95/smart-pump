<?php

namespace App\Controller;

use App\Entity\Station;
use App\Form\StationAddType;
use App\Repository\StationRepository;
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

class StationController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/stations", name="clients.stations")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        return $this->render('user-client/dashboard/station/index.html.twig', [
            'stations' => $paginator->paginate($this->getUser()->getStructureClient()->getStations(), $request->query->getInt('page', 1), 30)
        ]);
    }

    /**
     * ADD STATION
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/stations/new", name="clients.stations.create")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function add(Request $request): Response
    {
        $station = new Station();
        $form = $this->createForm(StationAddType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($station);

            $station->setStructureClient($this->getUser()->getStructureClient())
                ->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));

            $this->addFlash('success', 'Station enregistrée avec succes');
            $em->flush();

            return $this->redirectToRoute('clients.stations');
        }

        return $this->render('user-client/dashboard/station/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * EDIT STATION
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/stations/{id}/edit", name="clients.stations.edit")
     * @param Request $request
     * @param int $id
     * @param StationRepository $stationRepository
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, int $id, StationRepository $stationRepository): Response
    {
        $station = $stationRepository->find($id);
        $form = $this->createForm(StationAddType::class, $station)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $station->setUpdatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La station a été modifiée avec succes');
            return $this->redirectToRoute('clients.stations');
        }
        return $this->render('user-client/dashboard/station/edit.html.twig', ['form' => $form->createView(), 'station' => $station]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/stations/{id}/delete", name="clients.stations.delete", methods={"DELETE"})
     * @param integer $id
     * @param Request $request
     * @param StationRepository $stationRepository
     * @return RedirectResponse
     */
    public function delete(int $id, Request $request, StationRepository $stationRepository): Response
    {
        $station = $stationRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $station->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($station);
            $entityManager->flush();
            $this->addFlash('success', 'La station a été suprimée');
        }
        return $this->redirectToRoute('clients.stations');
    }
}
