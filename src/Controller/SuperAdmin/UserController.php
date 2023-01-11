<?php

namespace App\Controller\SuperAdmin;

use App\Entity\User;
use App\Entity\UserRole;
use App\Form\SuperAdmin\UserAddType;
use App\Form\SuperAdmin\UserEditType;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * LIST USERS
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/users", name="superAdmin.users")
     */
    public function index(Request $request, PaginatorInterface $paginator, UserRepository $userRepository): Response
    {
        return $this->render('super-admin/user/index.html.twig', [
            'users' => $paginator->paginate($userRepository->getUserAdmins(), $request->query->getInt('page', 1), 30)
        ]);
    }

    /**
     * ADD USER
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/users/new", name="superAdmin.users.create", methods={"GET", "POST"})
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

            $user->setPassword($encoder->encodePassword($user, $user->getPassword()))
                ->setIsEnabled(true)
                ->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $user->setIsAdmin(false);
            if ($request->get('isAdmin')) {
                $user->setIsAdmin(true);
            }

            $role = $userRoleRepository->findOneBy(['libelle' => 'SUPER_ADMIN']);

            if ($role == null) {
                $role = new UserRole();
                $role->setLibelle('SUPER_ADMIN')
                    ->setDescription('ROLE SUPER ADMIN DE SMART PUMP');
                $em->persist($role);
            }
            $user->addUserRole($role);

            $em->flush();
            $this->addFlash('success', 'Utilisateur enregistré avec succes');

            return $this->redirectToRoute('superAdmin.users');
        }

        return $this->render('super-admin/user/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * EDIT USER
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/users/{id}/edit", name="superAdmin.users.edit", methods={"GET", "POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param int $id
     * @param UserRepository $userRepository
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, UserPasswordEncoderInterface $encoder, int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()))
                ->setUpdatedAt(new DateTime('now', new DateTimeZone('GMT')));
            $em->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succes');
            return $this->redirectToRoute('superAdmin.users');
        }
        return $this->render('super-admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * CHANGE USER ENABLED DISABLED
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/users/{id}/change-enabled", name="superAdmin.users.changeEnabled")
     * @param int $id
     * @param UserRepository $userRepository
     * @return RedirectResponse
     */
    public function changeEnabledUser(int $id, UserRepository $userRepository): RedirectResponse
    {
        $user = $userRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        if ($this->getUser() == $user) {
            $this->addFlash('danger', 'Vous pouvez pas changer le status votre compte');
            return $this->redirectToRoute('superAdmin.users');
        }
        if ($user !== null) {
            if ($user->getIsEnabled()) {
                $user->setIsEnabled(false);
                $em->flush();
                $this->addFlash('success', 'Compte utilisateur désactivé avec succes');
            } else {
                $user->setIsEnabled(true);
                $em->flush();
                $this->addFlash('success', 'Compte utilisateur activé avec succes');
            }
        } else {
            $this->addFlash('danger', 'Aucun utilisateur trouvé');
        }
        return $this->redirectToRoute('superAdmin.users');
    }

    /**
     * CHANGE USER ENABLED DISABLED
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/users/{id}/change-admin", name="superAdmin.users.changeAdmin")
     * @param int $id
     * @param UserRepository $userRepository
     * @return RedirectResponse
     */
    public function changeStatusUser(int $id, UserRepository $userRepository): RedirectResponse
    {
        $user = $userRepository->find($id);
        if ($this->getUser() == $user) {
            $this->addFlash('danger', 'Vous pouvez pas changer le status de votre compte');
            return $this->redirectToRoute('superAdmin.users');
        }
        $em = $this->getDoctrine()->getManager();
        if ($user !== null) {
            if ($user->getIsAdmin()) {
                $user->setIsAdmin(false);
                $em->flush();
                $this->addFlash('success', 'Compte utilisateur admin desactivé avec succes');
            } else {
                $user->setIsAdmin(true);
                $em->flush();
                $this->addFlash('success', 'Compte utilisateur admin activé avec succes');
            }
        } else {
            $this->addFlash('danger', 'Aucun utilisateur trouvé');
        }
        return $this->redirectToRoute('superAdmin.users');
    }

    /**
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/users/{id}/detele", name="superAdmin.users.delete", methods={"DELETE"})
     * @param UserRepository $userRepository
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(UserRepository $userRepository, Request $request, int $id): Response
    {
        $user = $userRepository->find($id);

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);

            $entityManager->flush();

            $this->addFlash('success', 'Compte utilisateur suprimé avec succes');
        }
        return $this->redirectToRoute('superAdmin.users');
    }
}
