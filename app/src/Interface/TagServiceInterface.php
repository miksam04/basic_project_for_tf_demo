<?php

/**
 * Tag interface.
 */

namespace App\Interface;

use App\Entity\Tag;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TagServiceInterface.
 */
interface TagServiceInterface
{
    /**
     * Find by title.
     *
     * @param string $title the tag title
     *
     * @return Tag|null the tag object or null if not found
     */
    public function findOneByTitle(string $title): ?Tag;

    /**
     * Get tag by ID.
     *
     * @param int $id the tag ID
     *
     * @return Tag|null the tag object or null if not found
     */
    public function getTagById(int $id): ?Tag;

    /**
     * Get paginated tags.
     *
     * @param int $page the page number
     *
     * @return PaginationInterface the paginated tags
     */
    public function getPaginatedTags(int $page): PaginationInterface;

    /**
     * Save a tag.
     *
     * @param Tag $tag the tag entity to save
     *
     * @return void returns nothing
     */
    public function save(Tag $tag): void;

    /**
     * Delete a tag.
     *
     * @param Tag $tag the tag entity to delete
     *
     * @return void returns nothing
     */
    public function delete(Tag $tag): void;

    /**
     * Check if a tag can be deleted.
     *
     * @param Tag $tag the tag entity to check
     *
     * @return bool true if the tag can be deleted, false otherwise
     */
    public function canBeDeleted(Tag $tag): bool;
}
