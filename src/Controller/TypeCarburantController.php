<?php

namespace App\Controller;

use App\Entity\TypeCarburant;
use App\Form\TypeCarburantType;
use App\Repository\TypeCarburantRepository;
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

class TypeCarburantController extends AbstractController
{
    /**
     * LIST DES TYPES DE CARBURANT
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/carburants", name="clients.typeCarburants")
     * @param TypeCarburantRepository $typeCarburantRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(TypeCarburantRepository $typeCarburantRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // TODO VERIFICATION DES AUTRES STATIONS

        return $this->render('user-client/dashboard/typeCarburant/index.html.twig', [
            'typeCarburants' => $paginator->paginate($this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants(), $request->query->getInt('page', 1), 20)
        ]);
    }

    /**
     * ADD TYPE CARBURANT
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/carburants/new", name="clients.typeCarburants.create", methods={"GET","POST"})
     * @param Request $request
     * @param TypeCarburantRepository $typeCarburantRepository
     * @param UserRepository $userRepository
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, TypeCarburantRepository $typeCarburantRepository, UserRepository $userRepository): Response
    {
        $typeCarburant = new TypeCarburant();
        $form = $this->createForm(TypeCarburantType::class, $typeCarburant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($typeCarburant);

            $last = $typeCarburantRepository->findOneBy(['name' => $typeCarburant->getName(), 'station' => $this->getUser()->getStructureClient()->getStations()[0]]);
            if ($last != null) {
                $this->addFlash('error', 'Vous avez dejà ce type de carburant enregistré');
                return $this->render('user-client/dashboard/typeCarburant/add.html.twig', [
                    'form' => $form->createView(),
                ]);
            } else {
                $typeCarburant->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));
                $typeCarburant->setStation($this->getUser()->getStructureClient()->getStations()[0]);
                $manager->flush();

                $this->addFlash('success', 'Type de carburant enregistré');
                return $this->redirectToRoute('clients.typeCarburants');
            }
        }

        return $this->render('user-client/dashboard/typeCarburant/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * EDIT TYPE CARBURANT
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/dashboard/carburants/{id}/edit", name="clients.typeCarburants.edit", methods={"GET","POST"})
     * @param Request $request
     * @param int $id
     * @param TypeCarburantRepository $typeCarburantRepository
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, int $id, TypeCarburantRepository $typeCarburantRepository): Response
    {
        $typeCarburant = $typeCarburantRepository->find($id);
        $form = $this->createForm(TypeCarburantType::class, $typeCarburant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeCarburant->setUpdatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $last = $typeCarburantRepository->findOneBy(['name' => $typeCarburant->getName(), 'station' => $this->getUser()->getStructureClient()->getStations()[0]]);
            if ($last != null && $last->getId() !== $typeCarburant->getId()) {
                $this->addFlash('error', 'Vous avez dejà ce type de carburant enregistré');
                return $this->render('user-client/dashboard/typeCarburant/edit.html.twig', [
                    'form' => $form->createView(),
                    'typeCarburant' => $typeCarburant
                ]);
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le type de carburant a été modifié');

            return $this->redirectToRoute('clients.typeCarburants');
        }

        return $this->render('user-client/dashboard/typeCarburant/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * DELETE TYPE DE CARBURANT
     * @Route("/dashboard/carburants/{id}/delete", name="clients.typeCarburants.delete", methods={"DELETE"})
     * @param int $id
     * @param Request $request
     * @param TypeCarburantRepository $typeCarburantRepository
     * @return RedirectResponse
     */
    public function delete(int $id, Request $request, TypeCarburantRepository $typeCarburantRepository): RedirectResponse
    {
        $type = $typeCarburantRepository->find($id);

        if ($this->isCsrfTokenValid('delete' . $type->getId(), $request->request->get('_token'))) {
            $manager = $this->getDoctrine()->getManager();

            $manager->remove($type);
            $manager->flush();
            $this->addFlash('success', 'Le type de carburant a été supprimé');
        }
        return $this->redirectToRoute('clients.typeCarburants');
    }
}
