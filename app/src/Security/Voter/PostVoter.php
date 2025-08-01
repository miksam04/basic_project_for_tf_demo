<?php

/**
 * Post voter.
 */

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Post;

/**
 * Class PostVoter.
 *
 * This class is responsible for determining if a user can perform actions on a Post entity.
 */
final class PostVoter extends Voter
{
    public const DELETE = 'POST_DELETE';
    public const EDIT = 'POST_EDIT';
    public const CREATE = 'POST_CREATE';

    /**
     * Determines if the voter supports the given attribute and subject.
     *
     * @param string $attribute The attribute to check
     * @param mixed  $subject   The subject to check against
     *
     * @return bool True if the voter supports the attribute and subject, false otherwise
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (in_array($attribute, [self::EDIT, self::DELETE])) {
            return $subject instanceof Post;
        }

        return self::CREATE === $attribute;
    }

    /**
     * Determines if the user can perform the action on the subject.
     *
     * @param string         $attribute The attribute to check
     * @param mixed          $subject   The subject to check against
     * @param TokenInterface $token     The authentication token
     *
     * @return bool True if the user can perform the action, false otherwise
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }


        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            self::CREATE => $this->canCreate($user),
            default => false,
        };
    }

    /**
     * Checks if the user can delete the post.
     *
     * @param Post          $post The post to check
     * @param UserInterface $user The user performing the action
     *
     * @return bool True if the user can delete the post, false otherwise
     */
    private function canDelete(Post $post, UserInterface $user): bool
    {
        return $post->getAuthor() === $user || $user->hasRole('ROLE_ADMIN');
    }

    /**
     * Checks if the user can edit the post.
     *
     * @param Post          $post The post to check
     * @param UserInterface $user The user performing the action
     *
     * @return bool True if the user can edit the post, false otherwise
     */
    private function canEdit(Post $post, UserInterface $user): bool
    {
        return $post->getAuthor()->getId() === $user->getId();
    }

    /**
     * Checks if the user can create a post.
     *
     * @param UserInterface $user The user performing the action
     *
     * @return bool True if the user can create a post, false otherwise
     */
    private function canCreate(UserInterface $user): bool
    {
        return $user->hasRole('ROLE_ADMIN');
    }
}
