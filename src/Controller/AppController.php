<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app.index")
     */
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {
            if (in_array('ROLE_SUPERADMIN', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('superAdmin.index');
            } elseif (in_array('ROLE_CLIENT', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('client.dashboard.index');
            }

            // TODO A ENLEVER
            return $this->redirectToRoute('client.dashboard.index');
        }
    }

    /**
     * Page index des administrateurs
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/index", name="superAdmin.index")
     * @return Response
     */
    public function superAdminIndex(): Response
    {
        return $this->render('layouts/index-super-admin.html.twig');
    }

    /**
     * Page index dashboard client
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/utilisateur/dashboard/index", name="client.dashboard.index")
     * @return Response
     */
    public function adminDashboardIndex(): Response
    {
        if (!$this->getUser()->getIsAdmin()) {
            return $this->redirectToRoute('client.gestion.index');
        }
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();
        return $this->render('index-dashboard-admin.html.twig', [
            'prixEssence' => $typeCarburants[0]->getPrixLittre(),
            'prixGasoil' => $typeCarburants[1]->getPrixLittre(),
        ]);
    }

    /**
     * Page index dashboard client
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/utilisateur/gestion/index", name="client.gestion.index")
     * @return Response
     */
    public function adminGestionIndex(): Response
    {
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();
        return $this->render('index-gestion-admin.html.twig', [
            'prixEssence' => $typeCarburants[0]->getPrixLittre(),
            'prixGasoil' => $typeCarburants[1]->getPrixLittre(),
        ]);
    }
}
