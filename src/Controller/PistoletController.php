<?php

namespace App\Controller;

use App\Entity\Indexation;
use App\Entity\Pistolet;
use App\Entity\Pompe;
use App\Entity\TypeCarburant;
use App\Form\PistoletType;
use App\Repository\PistoletRepository;
use App\Repository\PompeRepository;
use App\Repository\StationRepository;
use App\Repository\TypeCarburantRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PistoletController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/pistolets", name="clients.pistolets")
     * @param PistoletRepository $pistoletRepository
     * @return Response
     */
    public function index(PistoletRepository $pistoletRepository): Response
    {
        // TODO A MODIIFIER POUR MULTI STATION
        return $this->render('user-client/dashboard/pistolet/index.html.twig', [
            'pistolets' => $pistoletRepository->getPistoletByStation($this->getUser()->getStructureClient()->getStations()[0]->getId())
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/pistolets/new", name="clients.pistolets.create", methods={"GET", "POST"})
     * @param Request $request
     * @param TypeCarburantRepository $typeCarburantRepository
     * @param PompeRepository $pompeRepository
     * @return Response
     * @throws Exception
     */
    public function create(Request $request, TypeCarburantRepository $typeCarburantRepository, PompeRepository $pompeRepository): Response
    {
        $pistolet = new Pistolet();
        $form = $this->createForm(PistoletType::class, $pistolet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pistolet);
            $pistolet->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $station = $this->getUser()->getStructureClient()->getStations()[0];
            $typeCarburant = $typeCarburantRepository->find($request->get('typeCarburant'));
            $pistolet->setTypeCarburant($typeCarburant);

            $pompe = $pompeRepository->getPompeByClientStructureAndNumero($this->getUser()->getStructureClient()->getId(), 'POMPE_' . $request->get('numeroPompe'));

            if ($pompe == null) {
                $pompe = new Pompe();
                $pompe->setStation($station)
                    ->setNumero('POMPE_' . $request->get('numeroPompe'))
                    ->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));
                $em->persist($pompe);
                $pistolet->setNumero(strtoupper($typeCarburant->getName()) . '_1');
            } else {
                $cpt = 1;
                foreach ($pompe->getPistolets() as $p) {
                    if ($p->getTypeCarburant() == $pistolet->getTypeCarburant()) {
                        $cpt++;
                    }
                }
                $pistolet->setNumero(strtoupper($pistolet->getTypeCarburant()->getName()) . '_' . $cpt);
            }
            $pistolet->setPompe($pompe);

            $em->flush();
            $this->addFlash('success', 'Le pistolet a été enregistré');
            return $this->redirectToRoute('clients.pistolets');
        }
        return $this->render('user-client/dashboard/pistolet/add.html.twig', [
            'form' => $form->createView(),
            'pistolet' => $pistolet,
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/pistolets/{id}/edit", name="clients.pistolets.edit", methods={"GET", "POST"})
     * @param int $id
     * @param PistoletRepository $pistoletRepository
     * @param Request $request
     * @param TypeCarburantRepository $typeCarburantRepository
     * @param PompeRepository $pompeRepository
     * @return Response
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function edit(int $id, PistoletRepository $pistoletRepository, Request $request, TypeCarburantRepository $typeCarburantRepository, PompeRepository $pompeRepository): Response
    {
        $pistolet = $pistoletRepository->find($id);
        $numeroPompe = explode('_', $pistolet->getPompe()->getNumero())[1];
        $form = $this->createForm(PistoletType::class, $pistolet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $pistolet->setUpdatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $station = $this->getUser()->getStructureClient()->getStations()[0];
            $typeCarburant = $typeCarburantRepository->find($request->get('typeCarburant'));
            $pistolet->setTypeCarburant($typeCarburant);
            $pompe = $pompeRepository->getPompeByClientStructureAndNumero($this->getUser()->getStructureClient()->getId(), 'POMPE_' . $request->get('numeroPompe'));
            if ($pompe == null) {
                $pompe = new Pompe();
                $pompe->setStation($station)
                    ->setNumero('POMPE_' . $request->get('numeroPompe'))
                    ->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));
                $em->persist($pompe);
                $pistolet->setNumero(strtoupper($typeCarburant->getName()) . '_1');
            } else {
                $cpt = 1;
                foreach ($pompe->getPistolets() as $p) {
                    if (($p->getTypeCarburant() == $pistolet->getTypeCarburant()) && ($p !== $pistolet)) {
                        $cpt++;
                    }
                }
                $pistolet->setNumero(strtoupper($pistolet->getTypeCarburant()->getName()) . '_' . $cpt);
            }

            $pistolet->setPompe($pompe);

            $em->flush();
            $this->addFlash('success', 'Le pistolet a été modifié');
            return $this->redirectToRoute('clients.pistolets');
        }


        return $this->render('user-client/dashboard/pistolet/edit.html.twig', [
            'pistolet' => $pistolet,
            'form' => $form->createView(),
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants(),
            'numeroPompe' => intval($numeroPompe)
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/pistolets/{id}/delete", name="clients.pistolets.delete")
     * @param int $id
     * @param Request $request
     * @param PistoletRepository $pistoletRepository
     * @return RedirectResponse
     */
    public function delete(int $id, Request $request, PistoletRepository $pistoletRepository): RedirectResponse
    {
        $pistolet = $pistoletRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $pistolet->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pistolet);
            $entityManager->flush();
            $this->addFlash('success', 'Le pistolet a été suprimé');
        }
        return $this->redirectToRoute('clients.pistolets');
    }

    /**
     * @Route("/get-pistolets-by-typeCarburant/{id}", methods={"GET"})
     * @param PistoletRepository $pistoletRepository
     * @param TypeCarburant $typeCarburant
     * @return JsonResponse
     */
    public function getPistoletByTypeCarburant(PistoletRepository $pistoletRepository, TypeCarburant $typeCarburant): JsonResponse
    {
        return new JsonResponse($this->serializePistolets($pistoletRepository->findBy(['typeCarburant' => $typeCarburant])));
    }

    public function serializePistolets($pistolets): array
    {
        $pistolets_array = [];

        foreach ($pistolets as $pistolet) {
            $p = [
                'id' => $pistolet->getId(),
                'numero' => $pistolet->getPompe()->getNumero().' | '.$pistolet->getNumero()
            ];
            $pistolets_array[] = $p;
        }

        return $pistolets_array;
    }
}
