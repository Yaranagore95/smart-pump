<?php

namespace App\Controller\Admin;

use App\Entity\Cuve;
use App\Entity\TypeCarburant;
use App\Form\CuveType;
use App\Repository\CuveRepository;
use App\Repository\TypeCarburantRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CuveController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/cuves", name="clients.cuves")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        // TODO A MODIFIER POUR MULTIPLE STATION
        return $this->render('user-client/dashboard/cuve/index.html.twig', [
            'cuves' => $paginator->paginate($this->getUser()->getStructureClient()->getStations()[0]->getCuves(), $request->query->getInt('page', 1), 30)
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/cuves/new", name="clients.cuves.create", methods={"GET", "POST"})
     * @param Request $request
     * @param TypeCarburantRepository $typeCarburantRepository
     * @param CuveRepository $cuveRepository
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, TypeCarburantRepository $typeCarburantRepository, CuveRepository $cuveRepository): Response
    {
        $cuve = new Cuve();
        $form = $this->createForm(CuveType::class, $cuve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($cuve);
            $cuve->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $cuve->setStock(0);
            $cuve->setIsActived(false);
            $cuve->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));
            $cuve->setStation($this->getUser()->getStructureClient()->getStations()[0]);
            $cuve->setPrixAchatMoyen(0);
            $cpt = 0;
            $cuves = $cuve->getStation()->getCuves();

            foreach ($cuves as $c) {
                if ($c->getTypeCarburant() === $cuve->getTypeCarburant()) {
                    $cpt++;
                }
            }

            $cuve->setNumero(strtoupper('cuve_' . $cuve->getTypeCarburant()->getName() . '_numero_' . ($cpt + 1)));
            $lastCuve = $cuveRepository->findOneBy(['station' => $this->getUser()->getStructureClient()->getStations()[0], 'numero' => $cuve->getNumero()]);

            if ($lastCuve !== null && $lastCuve !== $cuve) {
                $this->addFlash('danger', 'Cette cuve est déjà enregistrée');
                return $this->render('user-client/dashboard/cuve/add.html.twig', [
                    'form' => $form->createView(),
                    'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants()
                ]);
            }

            $manager->flush();

            $this->addFlash('success', 'La cuve a été enregistrée');
            return $this->redirectToRoute('clients.cuves');
        }

        return $this->render('user-client/dashboard/cuve/add.html.twig', [
            'form' => $form->createView(),
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/cuves/{id}/edit", name="clients.cuves.edit")
     * @param Request $request
     * @param CuveRepository $cuveRepository
     * @param int $id
     * @param TypeCarburantRepository $typeCarburantRepository
     * @return Response
     */
    public function edit(Request $request, CuveRepository $cuveRepository, int $id, TypeCarburantRepository $typeCarburantRepository): Response
    {
        // TODO A MODIFIER POUR MULTIPLE STATION
        $cuve = $cuveRepository->find($id);
        $form = $this->createForm(CuveType::class, $cuve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cuves = $cuve->getStation()->getCuves();
            $cuve->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));
            $cuve->setStation($this->getUser()->getStructureClient()->getStations()[0]);
            $cpt = 0;
            foreach ($cuves as $c) {
                if ($c->getTypeCarburant() === $cuve->getTypeCarburant()) {
                    $cpt++;
                }
            }

            $cuve->setNumero(strtoupper('cuve_' . $cuve->getTypeCarburant()->getName() . '_numero_' . ($cpt)));
            $lastCuve = $cuveRepository->findOneBy(['station' => $this->getUser()->getStructureClient()->getStations()[0], 'numero' => $cuve->getNumero()]);

            if ($lastCuve !== null && $lastCuve !== $cuve) {
                $this->addFlash('danger', 'Cette cuve est déjà enregistrée');
                return $this->render('user-client/dashboard/cuve/edit.html.twig', [
                    'form' => $form->createView(),
                    'cuve' => $cuve,
                    'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants()
                ]);
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La cuve a été modifiée');
            return $this->redirectToRoute('clients.cuves');
        }

        return $this->render('user-client/dashboard/cuve/edit.html.twig', [
            'form' => $form->createView(),
            'cuve' => $cuve,
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/cuves/{id}/delete", name="clients.cuves.delete")
     * @param Request $request
     * @param int $id
     * @param CuveRepository $cuveRepository
     * @return RedirectResponse
     */
    public function delete(Request $request, int $id, CuveRepository $cuveRepository): RedirectResponse
    {
        $cuve = $cuveRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $cuve->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cuve);
            $entityManager->flush();
            $this->addFlash('success', 'La cuve a été suprimé');
        }
        return $this->redirectToRoute('clients.cuves');
    }

    /**
     * @Route("/get-cuves-by-type-carburant/{id}")
     * @param TypeCarburant $typeCarburant
     * @param CuveRepository $cuveRepository
     * @return JsonResponse
     */
    public function getCuveByTypeCarburant(TypeCarburant $typeCarburant, CuveRepository $cuveRepository): JsonResponse
    {
        return new JsonResponse($this->serializeCuve($cuveRepository->findBy(['typeCarburant' => $typeCarburant])));
    }

    public function serializeCuve(array $cuves): array
    {
        $cuves_array = [];
        foreach ($cuves as $cuve) {
            $cu = [
                'id' => $cuve->getId(),
                'numero' => $cuve->getNumero()
            ];
            $cuves_array[] = $cu;
        }
        return $cuves_array;
    }
}
