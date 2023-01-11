<?php

namespace App\Controller\Admin;

use App\Entity\Indexation;
use App\Entity\VentePistolet;
use App\Form\IndexationAddType;
use App\Repository\IndexationRepository;
use App\Repository\PistoletRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionIndexationController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/indexations", name="gestion_indexations.index", methods={"GET", "POST"})
     * @throws Exception
     */
    public function index(Request $request, IndexationRepository $indexationRepository, PaginatorInterface $paginator, PistoletRepository $pistoletRepository): Response
    {
        $newIndex = new Indexation();
        $form = $this->createForm(IndexationAddType::class, $newIndex);
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $request->get('btnNewIndex') == '1') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($newIndex);
                $createdAt = new DateTime($request->get('createdAt'), new DateTimeZone('GMT'));
                $createdAt->modify('+1 minutes');
                $newIndex->setCreatedAt($createdAt);
                $pistolet = $pistoletRepository->find($request->get('pistolet'));
                $newIndex->setPistolet($pistolet);
                if ($pistolet->getIndexPistolet() > $newIndex->getValIndex()) {
                    $this->addFlash('danger', 'L\'index de ' . $pistolet->getPompe()->getNumero() . ' | ' . $pistolet->getNumero() . ' ne pêut pas diminué');
                    return $this->render('user-client/gestion/index/index.html.twig', [
                        'form' => $form->createView(),
                        'typeCarburants' => $typeCarburants,
                        'indexations' => $paginator->paginate($indexationRepository->getStationIndexations($this->getUser()->getStructureClient()->getStations()[0]->getId()), $request->query->getInt('page', 1), 60)
                    ]);
                }

                $diff = $newIndex->getValIndex() - $pistolet->getIndexPistolet();
                $newIndex->setDifference($diff);
                $pistolet->setIndexPistolet($newIndex->getValIndex());
                $pistolet->setUpdatedAt($createdAt);
                $ventePistolet = new VentePistolet();
                $ventePistolet->setCreatedAt($createdAt);
                $ventePistolet->setQuantite($diff);
                $ventePistolet->setPistolet($pistolet);
                $ventePistolet->setMontant($diff * $pistolet->getTypeCarburant()->getPrixLittre());
                $em->persist($ventePistolet);
                $em->flush();

                $this->addFlash('success', 'L\'index de ' . $pistolet->getPompe()->getNumero() . ' | ' . $pistolet->getNumero() . ' a été enregistré');
                return $this->redirectToRoute('gestion_indexations.index');
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $request->get('btnIndexByDate')) {
            $dateInf = new DateTime($request->get('dateInf'));
            $dateSup = new DateTime($request->get('dateSup'));
            $indexations = $paginator->paginate($indexationRepository->getStationIndexationsByDate($this->getUser()->getStructureClient()->getStations()[0]->getId(), $dateInf, $dateSup), $request->query->getInt('page', 1), 1000000000);
            $this->addFlash('success', 'Les index entre ' . $dateInf->format('d-m-Y') . ' et ' . $dateSup->format('d-m-Y'));
            return $this->render('user-client/gestion/index/index.html.twig', [
                'form' => $form->createView(),
                'typeCarburants' => $typeCarburants,
                'indexations' => $indexations
            ]);
        }


        return $this->render('user-client/gestion/index/index.html.twig', [
            'form' => $form->createView(),
            'typeCarburants' => $typeCarburants,
            'indexations' => $paginator->paginate($indexationRepository->getStationIndexations($this->getUser()->getStructureClient()->getStations()[0]->getId()), $request->query->getInt('page', 1), 60)
        ]);
    }
}
