<?php

/**
 * Tag service.
 */

namespace App\Service;

use App\Interface\TagServiceInterface;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Entity\Tag;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Service responsible for managing tags.
 *
 * This service provides methods to find tags by title and ID.
 */
class TagService implements TagServiceInterface
{
    public $paginator;
    public $postRepository;
    public $tagRepository;

    private const ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param TagRepository      $tagRepository  The tag repository
     * @param PaginatorInterface $paginator      The paginator service
     * @param PostRepository     $postRepository The post repository
     */
    public function __construct(TagRepository $tagRepository, PaginatorInterface $paginator, PostRepository $postRepository)
    {
        $this->tagRepository = $tagRepository;
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
    }

    /**
     * Find a tag by its title.
     *
     * @param string $title The tag title
     *
     * @return Tag|null The tag object or null if not found
     */
    public function findOneByTitle(string $title): ?Tag
    {
        return $this->tagRepository->findOneByTitle($title);
    }

    /**
     * Get tag by ID.
     *
     * @param int $id The tag ID
     *
     * @return Tag|null The tag object or null if not found
     */
    public function getTagById(int $id): ?Tag
    {
        return $this->tagRepository->find($id);
    }

    /**
     * Get paginated tags.
     *
     * @param int $page The page number
     *
     * @return PaginationInterface The paginated tags
     */
    public function getPaginatedTags(int $page): PaginationInterface
    {
        $query = $this->tagRepository->queryAll();

        return $this->paginator->paginate($query, $page, self::ITEMS_PER_PAGE);
    }

    /**
     * Save a tag.
     *
     * @param Tag $tag The tag to save
     */
    public function save(Tag $tag): void
    {
        $this->tagRepository->save($tag);
    }

    /**
     * Delete a tag.
     *
     * @param Tag $tag The tag to delete
     */
    public function delete(Tag $tag): void
    {
        $this->tagRepository->delete($tag);
    }

    /**
     * Check if a tag can be deleted.
     *
     * A tag can be deleted if it is not associated with any posts.
     *
     * @param Tag $tag The tag to check
     *
     * @return bool True if the tag can be deleted, false otherwise
     */
    public function canBeDeleted(Tag $tag): bool
    {
        try {
            $result = $this->postRepository->countByTag($tag);

            return $result <= 0;
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
}
