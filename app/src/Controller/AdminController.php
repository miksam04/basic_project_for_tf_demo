<?php

/**
 * AdminController class.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\AdminType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * AdminController handles administrative tasks related to users.
 */
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    /**
     * Lists all users.
     *
     * @param UserService $userService Service to manage user data
     * @param int         $page        Page number for pagination
     *
     * @return Response Rendered response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/admin/users', name: 'user_index')]
    public function index(UserService $userService, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $userService->getPaginatedUsers($page);

        return $this->render('admin/list.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * Edits a user.
     *
     * @param User                $user        User entity to edit
     * @param Request             $request     HTTP request object
     * @param UserService         $userService Service to manage user data
     * @param TranslatorInterface $translator  Translator service for translations
     *
     * @return Response Rendered response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/admin/users/{id}/edit', name: 'admin_user_edit')]
    public function edit(User $user, Request $request, UserService $userService, TranslatorInterface $translator): Response
    {
        $adminCountBefore = $userService->countAdmins();
        $rolesBefore = $user->getRoles();
        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rolesAfter = $form->get('roles')->getData();

            if (in_array('ROLE_ADMIN', $rolesBefore, true) && !in_array('ROLE_ADMIN', $rolesAfter, true) && $adminCountBefore <= 1) {
                $this->addFlash('warning', $translator->trans('admin.last_admin_cannot_remove'));

                return $this->redirectToRoute('user_index');
            }

            if ($user === $this->getUser() && $form->get('isBlocked')->getData()) {
                $this->addFlash('warning', $translator->trans('admin.cannot_block_self'));

                return $this->redirectToRoute('user_index');
            }

            $plainPassword = $form->get('plainPassword')->getData();
            $userService->updatePassword($user, $plainPassword);
            $userService->saveUser($user);
            $this->addFlash('success', $translator->trans('message.%entity%.updated_successfully', ['%entity%' => $translator->trans('entity.user')]));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
