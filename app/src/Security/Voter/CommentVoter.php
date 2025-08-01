<?php

/**
 * Post voter.
 */

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Comment;

/**
 * Class CommentVoter.
 *
 * This class is responsible for voting on whether a user can perform actions
 * related to comments, such as creating or deleting them.
 */
final class CommentVoter extends Voter
{
    public const DELETE = 'COMMENT_DELETE';
    public const CREATE = 'COMMENT_CREATE';
    public const EDIT = 'COMMENT_EDIT';

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
            return $subject instanceof Comment;
        }

        return self::CREATE === $attribute;
    }

    /**
     * Votes on whether the user can perform the action described by the attribute
     * on the given subject.
     *
     * @param string         $attribute The attribute to check
     * @param mixed          $subject   The subject to check against
     * @param TokenInterface $token     The authentication token of the user
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
            self::DELETE => $this->canDelete($subject, $user),
            self::CREATE => $this->canCreate($user),
            self::EDIT => $this->canEdit($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if the user can delete the comment.
     *
     * @param Comment       $comment The comment to check
     * @param UserInterface $user    The user performing the action
     *
     * @return bool True if the user can delete the comment, false otherwise
     */
    private function canDelete(Comment $comment, UserInterface $user): bool
    {
        return $user->hasRole('ROLE_ADMIN') || $comment->getEmail() === $user->getEmail();
    }

    /**
     * Checks if the user can create a comment.
     *
     * @param UserInterface $user The user performing the action
     *
     * @return bool True if the user can create a comment, false otherwise
     */
    private function canCreate(UserInterface $user): bool
    {
        return $user instanceof UserInterface && $user->hasRole('ROLE_USER');
    }

    /**
     * Checks if the user can edit the comment.
     *
     * @param Comment       $comment The comment to check
     * @param UserInterface $user    The user performing the action
     *
     * @return bool True if the user can edit the comment, false otherwise
     */
    private function canEdit(Comment $comment, UserInterface $user): bool
    {
        return $comment->getEmail() === $user->getEmail();
    }
}
