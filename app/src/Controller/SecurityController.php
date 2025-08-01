<?php

/**
 * SecurityController class.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * SecurityController handles user authentication.
 */
class SecurityController extends AbstractController
{
    /**
     * Displays the login form and handles authentication.
     *
     * @param AuthenticationUtils $authenticationUtils the authentication utils service to retrieve the last authentication error and last username
     * @param TranslatorInterface $translator          the translator service to translate messages
     *
     * @return Response returns the rendered login form or redirects to the post index if the user is already authenticated
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
        if ($this->getUser() instanceof UserInterface) {
            $this->addFlash('warning', $translator->trans('message.already_logged_in'));

            return $this->redirectToRoute('post_index');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Logs the user out.
     *
     * This method is intentionally left blank. The logout functionality is handled by Symfony's security system.
     *
     * @throws \LogicException
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
