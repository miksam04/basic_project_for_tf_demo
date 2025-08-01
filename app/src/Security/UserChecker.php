<?php

/**
 * User checker.
 */

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\User;

/**
 * Class UserChecker.
 *
 * This class checks user account status before authentication.
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator the translator service for translations
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Check pre-authentication user status.
     *
     * @param UserInterface $user the user to check
     *
     * @return void returns nothing
     *
     * @throws CustomUserMessageAccountStatusException if the user is not enabled
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof User && $user->isBlocked()) {
            throw new CustomUserMessageAccountStatusException($this->translator->trans('account_blocked_message'));
        }
    }

    /**
     * Check post-authentication user status.
     *
     * @param UserInterface $user the user to check
     *
     * @return void returns nothing
     */
    public function checkPostAuth(UserInterface $user): void
    {
        // No post-authentication checks needed in this implementation.
    }
}
