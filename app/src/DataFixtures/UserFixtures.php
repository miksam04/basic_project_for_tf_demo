<?php

/**
 * User fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixtures.
 *
 * This class is responsible for loading the initial data into the database.
 */
class UserFixtures extends AbstractBaseFixtures
{
    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher The password hasher
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Load the fixtures into the database.
     */
    protected function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(5, 'user', function (int $i) {
            $user = new User();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER->value]);
            $user->setNickname(mb_substr($this->faker->userName(), 0, 20));
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user1234'));


            return $user;
        });


        $this->createMany(2, 'admin', function (int $i) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value]);
            $user->setNickname($this->faker->userName());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'admin1234'));

            return $user;
        });
    }
}
