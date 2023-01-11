<?php

namespace App\Controller\SuperAdmin;

use App\Entity\StructureClient;
use App\Entity\User;
use App\Entity\UserRole;
use App\Form\SuperAdmin\StructureClientAddType;
use App\Repository\StructureClientRepository;
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

class UserClientController extends AbstractController
{
    /**
     * LIST CLIENTS
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/user-clients", name="superAdmin.userClients", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, UserRepository $userRepository): Response
    {
        return $this->render('super-admin/user-client/index.html.twig', [
            'users' => $paginator->paginate($userRepository->getUserClients(), $request->query->getInt('page', 1), 30)
        ]);
    }

    /**
     * ADD CLIENT
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/user-clients/new", name="superAdmin.userClients.create", methods={"GET", "POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param UserRoleRepository $userRoleRepository
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, UserPasswordEncoderInterface $encoder, UserRoleRepository $userRoleRepository): Response
    {
        $client = new User();
        $structure = new StructureClient();
        $structure->addUser($client);
        $form = $this->createForm(StructureClientAddType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($structure);

            $client->setPassword($encoder->encodePassword($client, $client->getPassword()))
                ->setIsEnabled(true)
                ->setIsAdmin(true)
                ->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));

            $structure->setCreatedAt(new DateTime('now', new DateTimeZone('GMT')));

            $role = $userRoleRepository->findOneBy(['libelle' => 'CLIENT']);

            if ($role == null) {
                $role = new UserRole();
                $role->setLibelle('CLIENT')
                    ->setDescription('ROLE CLIENT DE SMART PUMP');

                $em->persist($role);
            }

            $client->addUserRole($role);

            $em->flush();

            $this->addFlash('success', 'Client smart pump enregistré');
            return $this->redirectToRoute('superAdmin.userClients');
        }

        return $this->render('super-admin/user-client/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * DELETE STRUCTURE
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/user-clients/{id}/detele", name="superAdmin.userClients.delete", methods={"DELETE"})
     * @param Request $request
     * @param int $id
     * @param StructureClientRepository $structureClientRepository
     * @return RedirectResponse
     */
    public function delete(Request $request, int $id, StructureClientRepository $structureClientRepository): RedirectResponse
    {
        $structure = $structureClientRepository->find($id);

        if ($this->isCsrfTokenValid('delete' . $structure->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($structure);
            $entityManager->flush();
            $this->addFlash('success', 'Compte client suprimé avec succes');
        }
        return $this->redirectToRoute('superAdmin.userClients');
    }

    /**
     * @Security("is_granted('ROLE_SUPERADMIN') and user.getIsEnabled()")
     * @Route("/super-admin/user-clients/{id}/show", name="superAdmin.userClients.show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('super-admin/user-client/show.html.twig', ['user' => $user]);
    }
}
