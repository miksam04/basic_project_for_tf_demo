<?php

/**
 * App fixtures.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixtures.
 *
 * This class is responsible for loading the initial data into the database.
 */
class AppFixtures extends Fixture
{
    /**
     * Load the fixtures into the database.
     *
     * @param ObjectManager $manager The object manager
     */
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
