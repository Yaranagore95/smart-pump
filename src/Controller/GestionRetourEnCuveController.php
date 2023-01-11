<?php

namespace App\Controller;

use App\Entity\RetourEnCuve;
use App\Form\RetourEnCuveType;
use App\Repository\RetourEnCuveRepository;
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

class GestionRetourEnCuveController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/retour-en-cuves", name="gestion_retour_en_cuves.index", methods={"GET", "POST"})
     * @param RetourEnCuveRepository $retourEnCuveRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param TypeCarburantRepository $typeCarburantRepository
     * @return Response
     * @throws Exception
     */
    public function index(RetourEnCuveRepository $retourEnCuveRepository, PaginatorInterface $paginator, Request $request, TypeCarburantRepository $typeCarburantRepository): Response
    {
        $retourEnCuvues = $paginator->paginate($retourEnCuveRepository->getStationRetourEnCuves($this->getUser()->getStructureClient()->getStations()[0]->getId()), $request->query->getInt('page', 1), 30);
        $retourEnCuve = new RetourEnCuve();
        $form = $this->createForm(RetourEnCuveType::class, $retourEnCuve);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($retourEnCuve);

                $createdAt = new DateTime($request->get('createdAt'), new DateTimeZone('GMT'));
                $createdAt->modify('+1 minutes');
                $retourEnCuve->setCreatedAt($createdAt);
                $retourEnCuve->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));

                $em->flush();
                $this->addFlash('success', 'Le retour en cuve a été enregistré');
                return $this->redirectToRoute('gestion_retour_en_cuves.index');
            }
        }

        return $this->render('user-client/gestion/retour-en-cuve/index.html.twig', [
            'retourEnCuves' => $retourEnCuvues,
            'form' => $form->createView(),
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/retour-en-cuves/{id}/edit", name="gestion_retour_en_cuves.edit", methods={"GET", "POST"})
     * @throws Exception
     */
    public function edit(RetourEnCuve $retourEnCuve, Request $request, TypeCarburantRepository $typeCarburantRepository)
    {
        $form = $this->createForm(RetourEnCuveType::class, $retourEnCuve);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $createdAt = new DateTime($request->get('createdAt'), new DateTimeZone('GMT'));
            $createdAt->modify('+1 minutes');
            $retourEnCuve->setCreatedAt($createdAt);
            $retourEnCuve->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le retour en cuve a été modifié');
            return $this->redirectToRoute('gestion_retour_en_cuves.index');
        }
        return $this->render('user-client/gestion/retour-en-cuve/edit_retour_en_cuve.html.html.twig', [
            'form' => $form->createView(),
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants(),
            'retourEnCuve' => $retourEnCuve
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/retour-en-cuves/{id}/delete", name="gestion_retour_en_cuves.delete", methods={"DELETE"})
     * @param RetourEnCuve $retourEnCuve
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(RetourEnCuve $retourEnCuve, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $retourEnCuve->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($retourEnCuve);
            $entityManager->flush();
            $this->addFlash('success', 'Le retour en cuve a été suprimé');
        }
        return $this->redirectToRoute('gestion_retour_en_cuves.index');
    }
}
