<?php

/**
 * User controller.
 */

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Interface\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Controller responsible for managing user-related actions.
 */
class UserController extends AbstractController
{
    public $userService;
    public $translator;
    /**
     * @var UserPasswordHasherInterface
     */
    public $userPasswordHasher;

    /**
     * @param UserServiceInterface        $userService        the user service
     * @param TranslatorInterface         $translator         the translator service
     * @param UserPasswordHasherInterface $userPasswordHasher the password hasher service
     *
     * @return void
     */
    public function __construct(UserServiceInterface $userService, TranslatorInterface $translator, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userService = $userService;
        $this->translator = $translator;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Edit the user profile.
     *
     * @param Request $request the request object
     *
     * @return Response display the edit user form or redirect after successful update
     */
    #[Route('/user/edit', name: 'user_edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'action' => $this->generateUrl('user_edit'),
                'method' => 'PUT',
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $this->userService->updatePassword($user, $plainPassword);
            $this->userService->saveUser($user);

            $this->addFlash('success', $this->translator->trans('message.%entity%.updated_successfully', ['%entity%' => $this->translator->trans('entity.user')]));

            return $this->redirectToRoute('user_edit');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
