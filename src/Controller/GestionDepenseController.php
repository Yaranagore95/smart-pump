<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Entity\DetailDepense;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionDepenseController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/depenses", name="gestion_depense.index", methods={"GET", "POST"})
     * @param Request $request
     * @param DepenseRepository $depenseJournalierRepository
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws Exception
     */
    public function indexDepense(Request $request, DepenseRepository $depenseJournalierRepository, PaginatorInterface $paginator): Response
    {
        $depenses = $paginator->paginate($depenseJournalierRepository->findBy(['station' => $this->getUser()->getStructureClient()->getStations()[0]], ['createdAt' => 'DESC']), $request->query->getInt('page', 1), 30);
        $depense = new Depense();

        $form = $this->createForm(DepenseType::class, $depense);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($depense);
                $createdAt = new DateTime($request->get('createdAt'), new DateTimeZone('GMT'));
                $createdAt->modify('+1 minutes');
                $depense->setCreatedAt($createdAt);
                $depense->setStation($this->getUser()->getStructureClient()->getStations()[0]);
                $nbDetail = $request->get('hiddenVal');

                for ($i = 1; $i <= $nbDetail; $i++) {
                    $detail = new DetailDepense();
                    $detail->setDepense($depense);
                    $detail->setMontant($request->get('montant' . $i));
                    $detail->setLibelle($request->get('libelle' . $i));
                    $detail->setDescription($request->get('description' . $i));
                    $manager->persist($detail);
                    $manager->flush();
                }

                $manager->flush();
                $this->addFlash('success', 'Depense enregistrée');
                return $this->redirectToRoute('gestion_depense.index');
            }
        }

        return $this->render('user-client/gestion/depense/index.html.twig', [
            'depenses' => $depenses,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/depenses/{id}/edit", name="gestion_depense.edit")
     * @param Request $request
     * @param int $id
     * @param DepenseRepository $depenseJournalierRepository
     * @return Response
     */
    public function editDepense(Request $request, int $id, DepenseRepository $depenseJournalierRepository): Response
    {
        $depense = $depenseJournalierRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            foreach ($depense->getDetailDepenses() as $detailDepens) {
                $manager->remove($detailDepens);
                $manager->flush();
            }

            $nbDetail = $request->get('hiddenVal');

            for ($i = 1; $i <= $nbDetail; $i++) {
                $detail = new DetailDepense();
                $detail->setDepense($depense);
                $detail->setMontant($request->get('montant' . $i));
                $detail->setLibelle($request->get('libelle' . $i));
                $detail->setDescription($request->get('description' . $i));
                $manager->persist($detail);
                $manager->flush();
            }

            $this->addFlash('success', 'La depense a été modifiée');
            return $this->redirectToRoute('gestion_depense.index');
        }

        return $this->render('user-client/gestion/depense/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/depenses/{id}/delete", name="gestion_depense.deleteDepense")
     * @param Request $request
     * @param Depense $depense
     * @return Response
     */
    public function delete(Request $request, Depense $depense): Response
    {

        if ($this->isCsrfTokenValid('delete' . $depense->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($depense->getDetailDepenses() as $detailDepense) {
                $entityManager->remove($detailDepense);
            }
            $entityManager->remove($depense);
            $entityManager->flush();

            $this->addFlash('success', 'La depensé a été suprimée');
        }
        return $this->redirectToRoute('gestion_depense.index');
    }

    /**
     * @Route("/api/depense/depense-by-id/{id}", methods={"GET"})
     * @param int $id
     * @param DepenseRepository $depenseJournalierRepository
     * @return JsonResponse
     */
    public function getDepenseById(int $id, DepenseRepository $depenseJournalierRepository): JsonResponse
    {
        $depense = $depenseJournalierRepository->findOneBy(['id' => $id]);
        return new JsonResponse($this->parseDetail($depense->getDetailDepenses()), 200);
    }

    /**
     * @param $details
     * @return array
     */
    public function parseDetail($details): array
    {
        $arrayDetail = [];
        foreach ($details as $d) {
            $detail = [
                'id' => $d->getId(),
                'libelle' => $d->getLibelle(),
                'montant' => $d->getMontant(),
                'description' => $d->getDescription()
            ];
            $arrayDetail[] = $detail;
        }
        return $arrayDetail;
    }
}
