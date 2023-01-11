<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserRole;
use App\Form\SuperAdmin\UserAddType;
use App\Form\SuperAdmin\UserEditType;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserStationController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/users-station", name="clients.userStations")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user-client/dashboard/user/index.html.twig', [
            'users' => $userRepository->getStationUsers($this->getUser()->getStructureClient()->getId())
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/users-station/new", name="clients.userStations.add")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param UserRoleRepository $userRoleRepository
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, UserPasswordEncoderInterface $encoder, UserRoleRepository $userRoleRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserAddType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $user->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $role = $userRoleRepository->findOneBy(['libelle' => 'CLIENT']);
            if ($role == null) {
                $role = new UserRole();
                $role->setLibelle('CLIENT')
                    ->setDescription('ROLE CLIENT DE SMART PUMP');
                $em->persist($role);
            }
            $user->addUserRole($role);
            $user->setIsEnabled(true);
            $user->setStructureClient($this->getUser()->getStructureClient());
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            if ($request->get('isAdmin')) {
                $user->setIsAdmin(true);
            }else {
                $user->setIsAdmin(false);
            }
            $em->flush();
            $user->setImageFile(null);
            $this->addFlash('success', 'Utilisateur enregistré');
            return $this->redirectToRoute('clients.userStations');
        }

        return $this->render('user-client/dashboard/user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/dashboard/users-station/{id}/edit", name="clients.userStations.edit")
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $this->getDoctrine()->getManager()->flush();
            $user->setImageFile(null);
            $this->addFlash('success', 'Votre compte a été modifié');
            return $this->redirectToRoute('clients.userStations');
        }

        return $this->render('user-client/dashboard/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
