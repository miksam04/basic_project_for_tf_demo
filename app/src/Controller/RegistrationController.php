<?php

/**
 * Registration controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Interface\UserServiceInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RegistrationController.
 *
 * This controller handles user registration.
 */
class RegistrationController extends AbstractController
{
    public $userService;
    public $translator;
    /**
     * @var UserPasswordHasherInterface
     */
    public $userPasswordHasher;

    /**
     * constructor.
     *
     * @param UserServiceInterface        $userService        the user service
     * @param TranslatorInterface         $translator         the translator service
     * @param UserPasswordHasherInterface $userPasswordHasher the password hasher service
     */
    public function __construct(UserServiceInterface $userService, TranslatorInterface $translator, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userService = $userService;
        $this->translator = $translator;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Register a new user.
     *
     * @param Request $request the request object
     *
     * @return Response display the registration form or redirect after successful registration
     */
    #[\Symfony\Component\Routing\Attribute\Route('/register', name: 'user_register')]
    public function register(Request $request): Response
    {
        if ($this->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            $this->addFlash('warning', $this->translator->trans('message.already_logged_in'));

            return $this->redirectToRoute('post_index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $this->userService->updatePassword($user, $plainPassword);
            $this->userService->saveUser($user);
            $this->addFlash('success', $this->translator->trans('message.registration_success'));

            return $this->redirectToRoute('app_login');
        }


        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
