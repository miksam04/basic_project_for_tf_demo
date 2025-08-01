<?php

/**
 * Post fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use App\Entity\Tag;
use App\Interface\TagServiceInterface;
use App\Form\DataTransformer\TagsDataTransformer;

/**
 * Class PostFixtures.
 *
 * This class is responsible for loading the initial data into the database.
 */
class PostFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    private readonly TagsDataTransformer $tagsDataTransformer;

    /**
     * PostFixtures constructor.
     *
     * @param TagServiceInterface $tagService The tag service
     */
    public function __construct(private readonly TagServiceInterface $tagService)
    {
        $this->tagsDataTransformer = new TagsDataTransformer($this->tagService);
    }

    /**
     * Load the fixtures.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $tagCache = [];


        $this->createMany(15, 'post', function (int $i) use (&$tagCache) {
            $post = new Post();
            $post->setTitle($this->faker->sentence());
            $post->setContent($this->faker->paragraphs(3, true));
            $post->setAuthor($this->getRandomReference('user', User::class));
            $post->setCategory($this->getRandomReference('category', Category::class));
            $post->setStatus('published');

            $tagCount = $this->faker->numberBetween(1, 4);
            $tagTitles = [];
            for ($j = 0; $j < $tagCount; ++$j) {
                $tagTitle = ucfirst($this->faker->word());
                $tagTitle = trim(mb_substr($tagTitle, 0, 64));
                if ('' !== $tagTitle && !in_array($tagTitle, $tagTitles, true)) {
                    $tagTitles[] = $tagTitle;
                }
            }
            foreach ($tagTitles as $tagTitle) {
                if (!isset($tagCache[$tagTitle])) {
                    $tag = $this->tagService->findOneByTitle(strtolower($tagTitle));
                    if (!$tag instanceof Tag) {
                        $tag = new Tag();
                        $tag->setTitle($tagTitle);
                    }
                    $tagCache[$tagTitle] = $tag;
                }
                $post->addTag($tagCache[$tagTitle]);
            }

            return $post;
        });
    }

    /**
     * Get the dependencies.
     *
     * @return array The dependencies
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
