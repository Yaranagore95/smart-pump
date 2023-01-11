<?php

namespace App\Controller;

use App\Entity\GlobalStockage;
use App\Entity\Stockage;
use App\Form\StockageAddType;
use App\Repository\CuveRepository;
use App\Repository\GlobalStockageRepository;
use App\Repository\StockageRepository;
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

class GestionStockageController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/stockages", name="gestion_stockages.index", methods={"GET", "POST"})
     * @throws Exception
     */
    public function index(StockageRepository $stockageRepository, PaginatorInterface $paginator, Request $request, CuveRepository $cuveRepository, GlobalStockageRepository $globalStockageRepository): Response
    {
        $stockages = $paginator->paginate($stockageRepository->getStockageByStationId($this->getUser()->getStructureClient()->getStations()[0]->getId()), $request->query->getInt('page', 1), 30);
        $stockage = new Stockage();
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();
        $stockageGlobals = $paginator->paginate($globalStockageRepository->getGlobalStockagesByStationId($this->getUser()->getStructureClient()->getStations()[0]->getId()), $request->query->getInt('page', 1), 30);

        $form = $this->createForm(StockageAddType::class, $stockage);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid() && $form->isSubmitted()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($stockage);
                $cuve = $cuveRepository->find($request->get('cuve'));
                $createdAt = new DateTime($request->get('createdAt'), new DateTimeZone('GMT'));
                $createdAt->modify('+1 minutes');
                $stockage->setCreatedAt($createdAt);

                if ($cuve->getCapacity() < ($cuve->getStock() + $stockage->getQuantite() - $stockage->getManquant())) {
                    $this->addFlash('danger', 'La capacité de ' . $cuve->getNumero() . ' ne permet pas de stocker cette quantité');
                    return $this->render('user-client/gestion/stockage/index.html.twig', [
                        'stockages' => $stockages,
                        'typeCarburants' => $typeCarburants,
                        'form' => $form->createView(),
                        'stockageGlobals' => $stockageGlobals
                    ]);
                }
                if ($stockage->getQuantite() <= $stockage->getManquant()) {
                    $this->addFlash('danger', 'La quantité manquante peut pas être superieure à la quantité restante');
                    return $this->render('user-client/gestion/stockage/index.html.twig', [
                        'stockages' => $stockages,
                        'typeCarburants' => $typeCarburants,
                        'form' => $form->createView(),
                        'stockageGlobals' => $stockageGlobals
                    ]);
                }

                $stockage->setCuve($cuve);
                $result = $globalStockageRepository->getTypeCarburantLastGlobalStockage($stockage->getCuve()->getTypeCarburant());
                if ($result != null) {
                    $stockage->setGloabalStockage($result);
                } else {
                    $this->addFlash('danger', 'Pas de stockage globale pour ce stockage');
                    return $this->render('user-client/gestion/stockage/index.html.twig', [
                        'stockages' => $stockages,
                        'typeCarburants' => $typeCarburants,
                        'form' => $form->createView(),
                        'stockageGlobals' => $stockageGlobals
                    ]);
                }
                if ($cuve->getStock() === 0) {
                    $cuve->setLastPrixAchatMoyen($cuve->getPrixAchatMoyen());
                    $cuve->setPrixAchatMoyen($stockage->getPrixUnitaire());
                } else {
                    $pamp = ($cuve->getStock() * $cuve->getPrixAchatMoyen() + ($stockage->getQuantite() - $stockage->getManquant()) * $stockage->getPrixUnitaire()) / ($cuve->getStock() + $stockage->getQuantite() - $stockage->getManquant());
                    $cuve->setLastPrixAchatMoyen($cuve->getPrixAchatMoyen());
                    $cuve->setPrixAchatMoyen($pamp);
                }

                $cuve->setStock($cuve->getStock() + $stockage->getQuantite() - $stockage->getManquant());
                $cuve->setUpdatedAt($createdAt);

                $last = $stockageRepository->findOneBy(['cuve' => $cuve, 'isLast' => true]);
                if ($last !== null) {
                    $last->setIsLast(false);
                }

                $stockage->setIsLast(true);
                $stockage->setQuantite($stockage->getQuantite() - $stockage->getManquant());

                $em->flush();

                $this->addFlash('success', 'Le stockage de ' . $cuve->getNumero() . ' a été enregistré !');

                return $this->redirectToRoute('gestion_stockages.index');
            }
        }
        return $this->render('user-client/gestion/stockage/index.html.twig', [
            'stockages' => $stockages,
            'typeCarburants' => $typeCarburants,
            'form' => $form->createView(),
            'stockageGlobals' => $stockageGlobals
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/stockages/{id}/edit", name="gestion_stockages.edit", methods={"GET", "POST"})
     * @param Stockage $stockage
     * @param Request $request
     * @return Response
     */
    public function edit(Stockage $stockage, Request $request): Response
    {
        // TODO VERIFICATION SI LE STOCK DE LA CUVE NA PAS CHANGER
        $form = $this->createForm(StockageAddType::class, $stockage);
        $lastStockage = null;

        $form->handleRequest($request);
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            dd($lastStockage);
        }
        return $this->render('user-client/gestion/stockage/edit.html.twig', [
            'stockage' => $stockage,
            'form' => $form->createView(),
            'typeCarburants' => $typeCarburants
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/stockages/add-stockage-global", name="gestion_stockages.addGlobalStockage", methods={"POST"})
     * @param Request $request
     * @param TypeCarburantRepository $typeCarburantRepository
     * @return RedirectResponse
     * @throws Exception
     */
    public function addGlobalStockage(Request $request, TypeCarburantRepository $typeCarburantRepository): RedirectResponse
    {
        $globalStockage = new GlobalStockage();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $createdAt = new DateTime($request->get('createdAt'), new DateTimeZone('GMT'));
            $createdAt->modify('+1 minutes');
            $globalStockage->setCreatedAt($createdAt);
            $qte = intval($request->get('quantite'));
            $manquant = intval($request->get('manquant'));
            $prixAchat = intval($request->get('prixAchat'));

            if (!is_int($qte) || !is_int($manquant) || !is_int($prixAchat)) {
                $this->addFlash('danger', 'Impossible d\'enregistrer ce stockage global');
                return $this->redirectToRoute('gestion_stockages.index');
            }

            if ($manquant >= $qte) {
                $this->addFlash('danger', 'Impossible d\'enregistrer ce stockage global');
                return $this->redirectToRoute('gestion_stockages.index');
            }

            $globalStockage->setManquant($manquant)
                ->setPrixAchat($prixAchat)
                ->setQuantite($qte - $manquant)
                ->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));

            $em->persist($globalStockage);
            $em->flush();
            $this->addFlash('success', 'Le stockage global a été enregistré !');
        }

        return $this->redirectToRoute('gestion_stockages.index');
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/stockages/{id}/edit-stockage-global", name="gestion_stockages.editGlobalStockage", methods={"GET", "POST"})
     * @param GlobalStockage $globalStockage
     * @param Request $request
     * @param TypeCarburantRepository $typeCarburantRepository
     * @return Response
     * @throws Exception
     */
    public function editGlobalStockage(GlobalStockage $globalStockage, Request $request, TypeCarburantRepository $typeCarburantRepository): Response
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $createdAt = new DateTime($request->get('createdAt'), new DateTimeZone('GMT'));
            $createdAt->modify('+1 minutes');
            $globalStockage->setCreatedAt($createdAt);
            $qte = intval($request->get('quantite'));
            $manquant = intval($request->get('manquant'));
            $prixAchat = intval($request->get('prixAchat'));

            if (!is_int($qte) || !is_int($manquant) || !is_int($prixAchat)) {
                $this->addFlash('danger', 'Impossible d\'modifier ce stockage global');
                return $this->redirectToRoute('gestion_stockages.index');
            }

            if ($manquant >= $qte) {
                $this->addFlash('danger', 'Impossible d\'enregistrer ce stockage global');
                return $this->redirectToRoute('gestion_stockages.index');
            }

            $globalStockage->setManquant($manquant)
                ->setPrixAchat($prixAchat)
                ->setQuantite($qte - $manquant)
                ->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le stockage gloabal a été modifié');
            return $this->redirectToRoute('gestion_stockages.index');
        }
        return $this->render('user-client/gestion/stockage/edit_global_stockage.html.twig', [
            'globalStockage' => $globalStockage,
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/global-stockage/{id}/delete", name="gestion_stockages.deleteGlobalStockage", methods={"DELETE"})
     * @param GlobalStockage $globalStockage
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteGlobalStockage(GlobalStockage $globalStockage, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $globalStockage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $stockages = $globalStockage->getStockages();
            foreach ($stockages as $stockage) {
                $stockage->setGloabalStockage(null);
                $entityManager->flush();
            }
            $entityManager->remove($globalStockage);
            $entityManager->flush();
            $this->addFlash('success', 'Le stockage global a été suprimé');
        }
        return $this->redirectToRoute('gestion_stockages.index');
    }
}
