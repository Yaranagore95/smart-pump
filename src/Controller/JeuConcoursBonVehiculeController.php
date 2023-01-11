<?php

namespace App\Controller;

use App\Entity\JeuConcoursBonVehicule;
use App\Form\JeuConcoursBonVehiculeType;
use App\Repository\JeuConcoursBonVehiculeRepository;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
 * @Route("/gestion")
 */
class JeuConcoursBonVehiculeController extends AbstractController
{
    /**
     * @Route("/jeu-concours-bons-vehicule", name="jeu_concours_bon_vehicule_index", methods={"GET"})
     * @param Request $request
     * @param JeuConcoursBonVehiculeRepository $jeuConcoursBonVehiculeRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, JeuConcoursBonVehiculeRepository $jeuConcoursBonVehiculeRepository, PaginatorInterface $paginator): Response
    {

        $jeu_concours_bon_vehicules = $paginator->paginate(
            $jeuConcoursBonVehiculeRepository->findBy(
                array(), ['createdAt' => 'DESC']), $request->query->getInt('page', 1), 40);

        return $this->render('user-client/gestion/jeu_concours_bon_vehicule/index.html.twig', [
            'jeu_concours_bon_vehicules' => $jeu_concours_bon_vehicules
        ]);
    }

    /**
     * @Route("/jeu-concours-bons-vehicule/vehicules", name="jeu_concours_vehicule_index", methods={"GET"})
     * @param JeuConcoursBonVehiculeRepository $jeuConcoursBonVehiculeRepository
     * @return Response
     */
    public function indexVehicule(JeuConcoursBonVehiculeRepository $jeuConcoursBonVehiculeRepository): Response
    {
        $datas = $jeuConcoursBonVehiculeRepository->getDistinctVehicules();
        $vehicules = [];
        foreach ($datas as $d) {
            $data = $jeuConcoursBonVehiculeRepository->getQteByMatricule($d['matricule']);
            $v = new JeuConcoursBonVehicule();
            $v->setMatricule($d['matricule']);
            $v->setQteTotal($data[0]['somme']);
            $v->setMontantTotal($data[0]['montant']);

            $vehicules[] = $v;
        }

        return $this->render('user-client/gestion/jeu_concours_bon_vehicule/index-by-vehicule.html.twig', [
            'vehicules' => $vehicules
        ]);
    }

    /**
     * @Route("/jeu-concours-bons-vehicule/new", name="jeu_concours_bon_vehicule_new", methods={"GET","POST"})
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $jeuConcoursBonVehicule = new JeuConcoursBonVehicule();
        $form = $this->createForm(JeuConcoursBonVehiculeType::class, $jeuConcoursBonVehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($jeuConcoursBonVehicule);
            $jeuConcoursBonVehicule->setCreatedAt(new DateTimeImmutable($request->get('createdAt'), new DateTimeZone('GMT')));
            $entityManager->flush();
            $this->addFlash('success', 'Le bon de ' . $jeuConcoursBonVehicule->getMatricule() . ' a été enregistré !');

            return $this->redirectToRoute('jeu_concours_bon_vehicule_new');
        }

        return $this->render('user-client/gestion/jeu_concours_bon_vehicule/new.html.twig', [
            'jeu_concours_bon_vehicule' => $jeuConcoursBonVehicule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/jeu-concours-bons-vehicule/{id}", name="jeu_concours_bon_vehicule_show", methods={"GET"})
     */
    public function show(JeuConcoursBonVehicule $jeuConcoursBonVehicule): Response
    {
        return $this->render('user-client/gestion/jeu_concours_bon_vehicule/show.html.twig', [
            'jeu_concours_bon_vehicule' => $jeuConcoursBonVehicule,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="jeu_concours_bon_vehicule_edit", methods={"GET","POST"})
     * @throws Exception
     */
    public function edit(Request $request, JeuConcoursBonVehicule $jeuConcoursBonVehicule): Response
    {
        $form = $this->createForm(JeuConcoursBonVehiculeType::class, $jeuConcoursBonVehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jeuConcoursBonVehicule->setCreatedAt(new DateTimeImmutable($request->get('createdAt'), new DateTimeZone('GMT')));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('jeu_concours_bon_vehicule_index');
        }

        return $this->render('user-client/gestion/jeu_concours_bon_vehicule/edit.html.twig', [
            'jeu_concours_bon_vehicule' => $jeuConcoursBonVehicule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/jeu-concours-bons-vehicule/{id}", name="jeu_concours_bon_vehicule_delete", methods={"POST"})
     */
    public function delete(Request $request, JeuConcoursBonVehicule $jeuConcoursBonVehicule): Response
    {
        if ($this->isCsrfTokenValid('delete' . $jeuConcoursBonVehicule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($jeuConcoursBonVehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('jeu_concours_bon_vehicule_index');
    }

    /**
     * @Route("/jeu-concours-bons-vehicule/recherche-vehicule/matricule", name="jeu_concours_bon_vehicule_find", methods={"POST", "GET"})
     * @param Request $request
     * @param JeuConcoursBonVehiculeRepository $jeuConcoursBonVehiculeRepository
     * @return Response
     */
    public function getVehicule(Request $request, JeuConcoursBonVehiculeRepository $jeuConcoursBonVehiculeRepository)
    {
        $matricule = $request->request->get('matricule');
        $bons = $jeuConcoursBonVehiculeRepository->findBy(['matricule' => $matricule], ['createdAt' => 'DESC']);

        return $this->render('user-client/gestion/jeu_concours_bon_vehicule/show.html.twig', [
            'bons' => $bons,
            'datas' => $jeuConcoursBonVehiculeRepository->getQteByMatricule($matricule)
        ]);
    }
}
