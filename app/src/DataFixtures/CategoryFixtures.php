<?php

/**
 * Category fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Class CategoryFixtures.
 *
 * This class is responsible for loading the initial data into the database.
 */
class CategoryFixtures extends AbstractBaseFixtures
{
    /**
     * Load the fixtures into the database.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(10, 'category', function (int $i) {
            $category = new Category();
            $category->setName($this->faker->unique()->word());

            return $category;
        });

        $this->manager->flush();
    }
}
