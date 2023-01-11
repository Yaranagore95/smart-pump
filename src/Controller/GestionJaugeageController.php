<?php

namespace App\Controller;

use App\Entity\Jaugeage;
use App\Entity\VenteCuve;
use App\Form\JaugeageAddType;
use App\Repository\CuveMesureRepository;
use App\Repository\CuveRepository;
use App\Repository\JaugeageRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionJaugeageController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/jaugeages", name="gestion_jaugeages.index", methods={"GET", "POST"})
     * @throws Exception
     */
    public function index(JaugeageRepository $jaugeageRepository, PaginatorInterface $paginator, Request $request, CuveRepository $cuveRepository, CuveMesureRepository $cuveMesureRepository): Response
    {
        $jaugeages = $paginator->paginate(
            $jaugeageRepository->getJaugeageByStation($this->getUser()->getStructureClient()->getStations()[0]->getId()),
            $request->query->getInt('page', 1), 30);
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();
        $jaugeage = new Jaugeage();
        $form = $this->createForm(JaugeageAddType::class, $jaugeage);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($jaugeage);
                $createdAt = new DateTime($request->get('createdAt'), new DateTimeZone('GMT'));
                $createdAt->modify('+1 minutes');
                $jaugeage->setCreatedAt($createdAt);
                $cuve = $cuveRepository->find($request->get('cuve'));
                $jaugeage->setCuve($cuve);
                $level = intval($jaugeage->getQuantite());
                $mesure = $cuveMesureRepository->findOneBy(['levelCm' => $level, 'cuve' => $cuve]);
                $valVirgule = $jaugeage->getQuantite() - $level;

                if ($valVirgule !== 0) {
                    $nextMesure = $cuveMesureRepository->findOneBy(['levelCm' => ($level + 1), 'cuve' => $cuve]);
                }

                if ($mesure === null) {
                    $this->addFlash('danger', 'Impossible de trouver cette valeur pour la ' . $cuve->getNumero());

                    return $this->render('user-client/gestion/jaugeage/index.html.twig', [
                        'jaugeages' => $jaugeages,
                        'form' => $form->createView(),
                        'typeCarburants' => $typeCarburants
                    ]);
                }

                if ($valVirgule !== 0 && $nextMesure === null) {
                    $this->addFlash('danger', 'Impossible de trouver le niveau ' . ($level + 1) . ' pour la ' . $cuve->getNumero());

                    return $this->render('user-client/gestion/jaugeage/index.html.twig', [
                        'jaugeages' => $jaugeages,
                        'form' => $form->createView(),
                        'typeCarburants' => $typeCarburants
                    ]);
                }

                if ($valVirgule !== 0) {
                    $qte = $mesure->getVolume() + ($nextMesure->getVolume() - $mesure->getVolume()) * $valVirgule;
                } else {
                    $qte = $mesure->getVolume();
                }

                $diff = $cuve->getStock() - $qte;

                if ($diff < 0) {
                    $this->addFlash('danger', 'La quantité jaugée peut pas être superieur au stock de ' . $cuve->getNumero());

                    return $this->render('user-client/gestion/jaugeage/index.html.twig', [
                        'jaugeages' => $jaugeages,
                        'form' => $form->createView(),
                        'typeCarburants' => $typeCarburants
                    ]);
                }

                $venteCuve = new VenteCuve();
                $venteCuve->setCreatedAt($createdAt);
                $venteCuve->setQuantite($diff);
                $venteCuve->setMontantAchat($venteCuve->getQuantite() * $cuve->getPrixAchatMoyen());
                $venteCuve->setMontantVente($venteCuve->getQuantite() * $cuve->getTypeCarburant()->getPrixLittre());
                $venteCuve->setGain($venteCuve->getMontantVente() - $venteCuve->getMontantAchat());
                $venteCuve->setCuve($cuve);

                $lastJauageage = $jaugeageRepository->findOneBy(['cuve' => $cuve, 'isLast' => true]);
                if ($lastJauageage != null) {
                    $lastJauageage->setIsLast(false);
                    $lastJauageage->setUpdatedAt($createdAt);
                }

                $jaugeage->setIsLast(true);
                $jaugeage->setQuantite($qte);

                $cuve->setStock($qte);
                $cuve->setUpdatedAt($createdAt);
                $em->persist($venteCuve);
                $em->flush();

                $this->addFlash('success', 'Le jaugeage de ' . $cuve->getNumero() . ' a été enregistré');
                return $this->redirectToRoute('gestion_jaugeages.index');
            }
        }
        return $this->render('user-client/gestion/jaugeage/index.html.twig', [
            'jaugeages' => $jaugeages,
            'form' => $form->createView(),
            'typeCarburants' => $typeCarburants,
        ]);
    }
}
