<?php

namespace App\Controller;

use App\Entity\BonChauffeur;
use App\Entity\Chauffeur;
use App\Entity\TypeVehicule;
use App\Entity\VehiculeChauffeur;
use App\Form\BonChauffeurType;
use App\Form\ChauffeurType;
use App\Form\TypeVehiculeType;
use App\Form\VehiculeChauffeurType;
use App\Repository\BonChauffeurRepository;
use App\Repository\ChauffeurRepository;
use App\Repository\TypeCarburantRepository;
use App\Repository\TypeVehiculeRepository;
use App\Repository\VehiculeChauffeurRepository;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionJeuConcoursController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours", name="gestion_jeux_concours.index", methods={"GET", "POST"})
     * @param Request $request
     * @param TypeVehiculeRepository $typeVehiculeRepository
     * @param ChauffeurRepository $chauffeurRepository
     * @param VehiculeChauffeurRepository $vehiculeChauffeurRepository
     * @param BonChauffeurRepository $bonChauffeurRepository
     * @param TypeCarburantRepository $typeCarburantRepository
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, TypeVehiculeRepository $typeVehiculeRepository, ChauffeurRepository $chauffeurRepository, VehiculeChauffeurRepository $vehiculeChauffeurRepository, BonChauffeurRepository $bonChauffeurRepository, TypeCarburantRepository $typeCarburantRepository, PaginatorInterface $paginator): Response
    {
        $newChauffeur = new Chauffeur();
        $formAddChauffeur = $this->createForm(ChauffeurType::class, $newChauffeur);
        $typeVehicules = $this->getUser()->getStructureClient()->getStations()[0]->getTypeVehicules();
        $vehicules = $paginator->paginate($vehiculeChauffeurRepository->getVehiculesByStationId($this->getUser()->getStructureClient()->getStations()[0]->getId()), $request->query->getInt('vehicules', 1), 10, array(
            'pageParameterName' => 'vehicules'
        ));
        $bons = $paginator->paginate($bonChauffeurRepository->getBonChaffeursByStationId($this->getUser()->getStructureClient()->getStations()[0]->getId()), $request->query->getInt('bons', 1), 10, array(
            'pageParameterName' => 'bons'
        ));

        $chauffeurs = $paginator->paginate($chauffeurRepository->getChauffeurs(), $request->query->getInt('chauffeurs', 1), 10, array(
            'pageParameterName' => 'chauffeurs'
        ));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $em = $this->getDoctrine()->getManager();
            if ($request->get('btnAddChauffeur') === '1') {
                $formAddChauffeur->handleRequest($request);
                if ($formAddChauffeur->isSubmitted() && $formAddChauffeur->isValid()) {
                    $newChauffeur->setTelephone('');
                    $em->persist($newChauffeur);
                    $newChauffeur->setDateNaissance(new DateTimeImmutable($request->get('dateNaissance')));
                    $newChauffeur->setTypeVehicule($typeVehiculeRepository->find($request->get('typeVehicule')));
                    $newChauffeur->setCreatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')));
                    $newChauffeur->setUpdatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')));

                    $lastChauffeur = $chauffeurRepository->findOneBy(['nom' => $newChauffeur->getNom(), 'prenom' => $newChauffeur->getPrenom(), 'dateNaissance' => $newChauffeur->getDateNaissance()]);

                    if ($lastChauffeur != null) {
                        $this->addFlash('danger', 'Un autre chauffeur avec les mêmes infos etait enregistré');
                    }
                    if ($newChauffeur->getTelephone() != null) {
                        $chaffeurByTelephone = $chauffeurRepository->findOneBy(['telephone' => $newChauffeur->getTelephone()]);
                        if ($chaffeurByTelephone != null && $newChauffeur->getTelephone() != null && $newChauffeur->getTelephone() != '') {
                            $this->addFlash('danger', 'Un autre chauffeur avec le même numero de téléphone existe déjà');
                            return $this->redirectToRoute('gestion_jeux_concours.index');
                        }
                    }

                    if ($request->get('dateNaissance') != null) {
                        $newChauffeur->setDateNaissance(new DateTimeImmutable($request->get('dateNaissance'), new DateTimeZone('GMT')));
                    }

                    $em->flush();
                    $this->addFlash('success', 'Le chauffeur a été enregistré');
                    return $this->redirectToRoute('gestion_jeux_concours.index');
                }
            }

            if ($request->get('btnAddTypeVehicule') === '1') {
                $newTypeVehicule = new TypeVehicule();
                $newTypeVehicule->setName($request->get('name'))
                    ->setDescription($request->get('description'))
                    ->setQteRecompense($request->get('qteRecompense'));
                $newTypeVehicule->setStation($this->getUser()->getStructureClient()->getStations()[0]);
                $newTypeVehicule->setCreatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')));
                $newTypeVehicule->setUpdatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')));

                $lastType = $typeVehiculeRepository->findOneBy(['name' => $newTypeVehicule->getName(), 'station' => $this->getUser()->getStructureClient()->getStations()[0]]);

                if ($lastType != null) {
                    $this->addFlash('danger', 'Ce type de vehicule est déjà enregistré');
                    return $this->redirectToRoute('gestion_jeux_concours.index');
                }
                $em->persist($newTypeVehicule);
                $em->flush();
                $this->addFlash('success', 'Le nouveau type de vehicule a été enregistré');
                return $this->redirectToRoute('gestion_jeux_concours.index');
            }

            if ($request->get('btnAddVehicule') === '1') {
                $vehicule = new VehiculeChauffeur();
                $vehicule->setImmatriculation($request->get('immatriculation'))
                    ->setDescription($request->get('description'))
                    ->setChauffeur($chauffeurRepository->find($request->get('chauffeur')))
                    ->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')))
                    ->setCreatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')))
                    ->setUpdatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')));

                $lastVehicule = $vehiculeChauffeurRepository->findOneBy(['immatriculation' => $vehicule->getImmatriculation()]);
                if ($lastVehicule != null) {
                    $this->addFlash('danger', 'Ce n° matricule existe déjà');
                    return $this->redirectToRoute('gestion_jeux_concours.index');
                }
                $em->persist($vehicule);
                $em->flush();
                $this->addFlash('success', 'Le vehicule a été enregistré');
                return $this->redirectToRoute('gestion_jeux_concours.index');
            }

            if ($request->get('btnAddBon') === '1') {
                $bon = new BonChauffeur();
                $vehicule = $vehiculeChauffeurRepository->find($request->get('vehiculeChauffeur'));
                $bon->setImageFile($request->files->get('pieceJointe'));

                $bon->setQuantite(floatval($request->get('quantite')));
                $bon->setMontant($vehicule->getTypeCarburant()->getPrixLittre() * $request->get('quantite'));
                $bon->setVehiculeChauffeur($vehiculeChauffeurRepository->find($request->get('vehiculeChauffeur')));
                $bon->setCreatedAt(new DateTimeImmutable($request->get('date'), new DateTimeZone('GMT')));
                $bon->setUpdatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')));
                $em->persist($bon);
                $em->flush();

                $this->addFlash('success', 'Le bon chauffeur a été enregistré');
                return $this->redirectToRoute('gestion_jeux_concours.index');
            }
        }

        return $this->render('user-client/gestion/jeu-concours/index.html.twig', [
            'typeVehicules' => $typeVehicules,
            'formChauffeur' => $formAddChauffeur->createView(),
            'bonChauffeurs' => $bons,
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants(),
            'vehicules' => $vehicules,
            'chauffeurs' => $chauffeurs
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/typeVehicule/{id}/detail", name="gestion_jeux_concours.typeVehicule.show", methods={"GET"})
     * @param TypeVehicule $typeVehicule
     * @param ChauffeurRepository $chauffeurRepository
     * @param VehiculeChauffeurRepository $vehiculeChauffeurRepository
     * @param BonChauffeurRepository $bonChauffeurRepository
     * @return Response
     */
    public function showTypeVehicule(TypeVehicule $typeVehicule, ChauffeurRepository $chauffeurRepository, VehiculeChauffeurRepository $vehiculeChauffeurRepository, BonChauffeurRepository $bonChauffeurRepository): Response
    {
        return $this->render('user-client/gestion/jeu-concours/showTypeVehicule.html.twig', [
            'chauffeurs' => $chauffeurRepository->findBy(['typeVehicule' => $typeVehicule]),
            'vehicules' => $vehiculeChauffeurRepository->getVehiculesByTypeVehiculeId($typeVehicule->getId()),
            'bons' => $bonChauffeurRepository->getBonChauffeursByTypeVehiculeId($typeVehicule->getId()),
            'typeVehicule' => $typeVehicule
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/chauffeur/{id}/detail", name="gestion_jeux_concours.chauffeur.show", methods={"GET"})
     * @param Chauffeur $chauffeur
     * @param BonChauffeurRepository $bonChauffeurRepository
     * @return Response
     */
    public function showChauffeur(Chauffeur $chauffeur, BonChauffeurRepository $bonChauffeurRepository): Response
    {
        return $this->render('user-client/gestion/jeu-concours/showChauffeur.html.twig', [
            'chauffeur' => $chauffeur,
            'bons' => $bonChauffeurRepository->getBonsByChauffeurId($chauffeur->getId())
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/vehicule/{id}/detail", name="gestion_jeux_concours.vehicule.show", methods={"GET"})
     * @param VehiculeChauffeur $vehiculeChauffeur
     * @return Response
     */
    public function showVehicule(VehiculeChauffeur $vehiculeChauffeur): Response
    {
        return $this->render('user-client/gestion/jeu-concours/showVehicule.html.twig', [
            'vehicule' => $vehiculeChauffeur
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/bon/{id}/detail", name="gestion_jeux_concours.bon.show", methods={"GET"})
     * @param BonChauffeur $bonChauffeur
     * @return Response
     */
    public function showBonChauffeur(BonChauffeur $bonChauffeur): Response
    {
        return $this->render('user-client/gestion/jeu-concours/showBon.html.twig', [
            'bon' => $bonChauffeur
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/type-vehicule/{id}/edit", name="gestion_jeux_concours.typeVehicule.edit", methods={"GET", "POST"})
     * @param Request $request
     * @param TypeVehicule $typeVehicule
     * @param TypeVehiculeRepository $typeVehiculeRepository
     * @return Response
     * @throws Exception
     */
    public function editTypeVehicule(Request $request, TypeVehicule $typeVehicule, TypeVehiculeRepository $typeVehiculeRepository): Response
    {
        $form = $this->createForm(TypeVehiculeType::class, $typeVehicule);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lastType = $typeVehiculeRepository->findOneBy(['name' => $typeVehicule->getName(), 'station' => $this->getUser()->getStructureClient()->getStations()[0]]);
            if ($lastType != null && $lastType->getId() != $typeVehicule->getId()) {
                $this->addFlash('danger', 'Ce type de vehicule existe déjà');
                return $this->redirectToRoute('gestion_jeux_concours.typeVehicule.edit', ['id' => $typeVehicule->getId()]);
            }

            $typeVehicule->setUpdatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')));

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le type de vehicule a été modifié');

            return $this->redirectToRoute('gestion_jeux_concours.index');
        }

        return $this->render('user-client/gestion/jeu-concours/editTypeVehicule.html.twig', [
            'formTypeVehicule' => $form->createView(),
            'typeVehicule' => $typeVehicule
        ]);
    }


    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/chauffeur/{id}/edit", name="gestion_jeux_concours.chauffeur.edit", methods={"GET", "POST"})
     * @throws Exception
     */
    public function editChauffeur(Chauffeur $chauffeur, Request $request, ChauffeurRepository $chauffeurRepository, TypeVehiculeRepository $typeVehiculeRepository)
    {
        $form = $this->createForm(ChauffeurType::class, $chauffeur);
        $form->handleRequest($request);
        $typeVehicules = $this->getUser()->getStructureClient()->getStations()[0]->getTypeVehicules();

        if ($form->isSubmitted() && $form->isValid()) {
            $lastChauffeur = $chauffeurRepository->findOneBy(['telephone' => $chauffeur->getTelephone()]);
            if ($lastChauffeur != null && $chauffeur->getTelephone() != null && $chauffeur->getTelephone() != '' && $lastChauffeur->getId() != $lastChauffeur->getId()) {
                $this->addFlash('danger', 'Un chauffeur avec le même numéro de téléphone existe déjà');
                return $this->redirectToRoute('gestion_jeux_concours.index');
            }

            $chauffeur->setUpdatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')));
            if ($request->get('dateNaissance') != null) {
                $chauffeur->setDateNaissance(new DateTimeImmutable($request->get('dateNaissance'), new DateTimeZone('GMT')));
            }
            $chauffeur->setTypeVehicule($typeVehiculeRepository->find($request->get('typeVehicule')));
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le chauffeur a été modifié');

            return $this->redirectToRoute('gestion_jeux_concours.index');
        }


        return $this->render('user-client/gestion/jeu-concours/editChauffeur.html.twig', [
            'chauffeur' => $chauffeur,
            'formChauffeur' => $form->createView(),
            'typeVehicules' => $typeVehicules
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/vehicule/{id}/edit", name="gestion_jeux_concours.vehicule.edit", methods={"GET", "POST"})
     * @throws Exception
     */
    public function editVehiculeChauffeur(VehiculeChauffeur $vehiculeChauffeur, Request $request, VehiculeChauffeurRepository $vehiculeChauffeurRepository, ChauffeurRepository $chauffeurRepository)
    {
        $form = $this->createForm(VehiculeChauffeurType::class, $vehiculeChauffeur);
        $form->handleRequest($request);
        $typeVehicules = $this->getUser()->getStructureClient()->getStations()[0]->getTypeVehicules();

        if ($form->isSubmitted() && $form->isValid()) {
            $lastVehicule = $vehiculeChauffeurRepository->findOneBy(['immatriculation' => $vehiculeChauffeur->getImmatriculation()]);

            if ($lastVehicule != null && $lastVehicule->getId() != $vehiculeChauffeur->getId()) {
                $this->addFlash('danger', 'Un vehicule avec le même n° matricule existe déjà');
                return $this->redirectToRoute('gestion_jeux_concours.index');
            }

            $vehiculeChauffeur->setUpdatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')));
            $vehiculeChauffeur->setChauffeur($chauffeurRepository->find($request->get('chauffeur')));
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le vécule a été modifié');
            return $this->redirectToRoute('gestion_jeux_concours.index');
        }

        return $this->render('user-client/gestion/jeu-concours/editVehicule.html.twig', [
            'vehicule' => $vehiculeChauffeur,
            'form' => $form->createView(),
            'typeVehicules' => $typeVehicules,
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/bon-chauffeur/{id}/edit", name="gestion_jeux_concours.bonChauffeur.edit", methods={"GET", "POST"})
     * @throws Exception
     */
    public function editBonChauffeur(BonChauffeur $bonChauffeur, Request $request, VehiculeChauffeurRepository $vehiculeChauffeurRepository): Response
    {
        $typeVehicules = $this->getUser()->getStructureClient()->getStations()[0]->getTypeVehicules();
        $form = $this->createForm(BonChauffeurType::class, $bonChauffeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bonChauffeur->setUpdatedAt(new DateTimeImmutable('now', new DateTimeZone('GMT')));
            $bonChauffeur->setVehiculeChauffeur($vehiculeChauffeurRepository->findOneBy(['id' => $request->get('vehiculeChauffeur')]));
            $bonChauffeur->setMontant($bonChauffeur->getQuantite() * $bonChauffeur->getVehiculeChauffeur()->getTypeCarburant()->getPrixLittre());

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le bon du chauffeur a été modifié');
            return $this->redirectToRoute('gestion_jeux_concours.index');
        }


        return $this->render('user-client/gestion/jeu-concours/editBonChauffeur.html.twig', [
            'typeVehicules' => $typeVehicules,
            'bon' => $bonChauffeur,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/type-vehicule/{id}/delete", name="gestion_jeux_concours.type-vehicule.delete", methods={"DELETE"})
     * @param TypeVehicule $typeVehicule
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteTypeVehicule(TypeVehicule $typeVehicule, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $typeVehicule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($typeVehicule);
            $entityManager->flush();
            $this->addFlash('success', 'Le type de véhicule a été suprimée');
        }
        return $this->redirectToRoute('gestion_jeux_concours.index');
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/chauffeur/{id}/delete", name="gestion_jeux_concours.chauffeur.delete", methods={"DELETE"})
     * @param Chauffeur $chauffeur
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteChauffeur(Chauffeur $chauffeur, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $chauffeur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($chauffeur);
            $entityManager->flush();
            $this->addFlash('success', 'Le chauffeur a été suprimé');
        }
        return $this->redirectToRoute('gestion_jeux_concours.index');
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/vehicule/{id}/delete", name="gestion_jeux_concours.vehicule.delete", methods={"DELETE"})
     * @param VehiculeChauffeur $vehiculeChauffeur
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteVehiculeChauffeur(VehiculeChauffeur $vehiculeChauffeur, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $vehiculeChauffeur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vehiculeChauffeur);
            $entityManager->flush();
            $this->addFlash('success', 'Le véhicule du chaffeur a été suprimé');
        }
        return $this->redirectToRoute('gestion_jeux_concours.index');
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/bon-chauffeur/{id}/delete", name="gestion_jeux_concours.bonChauffeur.delete", methods={"DELETE"})
     * @param BonChauffeur $bonChauffeur
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteBonChauffeur(BonChauffeur $bonChauffeur, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $bonChauffeur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($bonChauffeur);
            $entityManager->flush();
            $this->addFlash('success', 'Le bon du chaffeur a été suprimé');
        }
        return $this->redirectToRoute('gestion_jeux_concours.index');
    }


    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled() and user.getIsAdmin()")
     * @Route("/gestion/jeux-concours/recherche", name="gestion_jeux_concours.recherche", methods={"POST"})
     * @param Request $request
     * @param ChauffeurRepository $chauffeurRepository
     * @param VehiculeChauffeurRepository $vehiculeChauffeurRepository
     * @return RedirectResponse|Response
     */
    public function rechercheInformation(Request $request, ChauffeurRepository $chauffeurRepository, VehiculeChauffeurRepository $vehiculeChauffeurRepository)
    {
        $type = $request->get('typeRecherche');
        switch ($type) {
            case 1:
                $info = $request->get('infoChauffeur');
                $chauffeurs = $chauffeurRepository->getChauffeurByCriterer($info);
                if (empty($chauffeurs)) {
                    $this->addFlash('danger', 'Pas de résultat pour la recherche');
                    return $this->redirectToRoute('gestion_jeux_concours.index');
                } else {
                    $this->addFlash('success', 'Résultat de la recherche');
                    return $this->render('user-client/gestion/jeu-concours/resultatRecherche.html.twig', compact('chauffeurs'));
                }
            case 2:
                $info = $request->get('immatriculationChauffeur');
                $vehicules = $vehiculeChauffeurRepository->getVehiculesByCritere($info);
                if (empty($vehicules)) {
                    $this->addFlash('danger', 'Pas de résultat pour la recherche');
                    return $this->redirectToRoute('gestion_jeux_concours.index');
                } else {
                    $this->addFlash('success', 'Résultat de la recherche');
                    return $this->render('user-client/gestion/jeu-concours/resultatRecherche.html.twig', compact('vehicules'));
                }

            default:
                return $this->redirectToRoute('gestion_jeux_concours.index');
        }
    }
}
