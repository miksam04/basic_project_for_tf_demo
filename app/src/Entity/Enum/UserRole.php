<?php

/**
 * UserRole enum class.
 */

namespace App\Entity\Enum;

/**
 * Enum representing user roles.
 */
enum UserRole: string
{
    case ROLE_ADMIN = 'ROLE_ADMIN';
    case ROLE_USER = 'ROLE_USER';

    /**
     * Returns the label for the user role.
     *
     * @return string returns the label for the user role
     */
    public function label(): string
    {
        return match ($this) {
            UserRole::ROLE_ADMIN => 'role_admin',
            Userrole::ROLE_USER => 'role_user',
        };
    }
}
