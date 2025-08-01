<?php

/**
 * User repository.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * UserRepository class.
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry The registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Save a User entity.
     *
     * @param User $user  The user entity to save
     * @param bool $flush Whether to flush the changes immediately
     *
     * @return void returns nothing
     */
    public function saveUser(User $user, bool $flush = true): void
    {
        $this->GetEntityManager()->persist($user);
        $this->GetEntityManager()->flush();
    }

    /**
     * Query all users.
     *
     * @return User[] Returns an array of User objects
     */
    public function queryAll(): array
    {
        return $this->createQueryBuilder('u')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count the number of users with the 'ROLE_ADMIN' role.
     *
     * @return int The count of admin users
     */
    public function countAdmins(): int
    {
        $users = $this->findAll();
        $adminCount = 0;
        foreach ($users as $user) {
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                ++$adminCount;
            }
        }

        return $adminCount;
    }




    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
