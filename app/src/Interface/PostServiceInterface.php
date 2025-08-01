<?php

/**
 * Post interface.
 */

namespace App\Interface;

use App\Entity\Post;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Dto\PostListInputFiltersDto;
use App\Dto\PostListFiltersDto;

/**
 * Interface PostServiceInterface.
 */
interface PostServiceInterface
{
    /**
     * Return all posts.
     *
     * @param int                     $page    the page number
     * @param User|null               $user    the user object or null if not filtering by user
     * @param PostListInputFiltersDto $filters the filters for the post list
     *
     * @return Post[]
     */
    public function getPaginatedPosts(int $page, ?User $user, PostListInputFiltersDto $filters): PaginationInterface;

    /**
     * Return a post by its ID.
     *
     * @param int $id the post ID
     *
     * @return Post|null the post object or null if not found
     */
    public function getPostById(int $id): ?Post;

    /**
     * Return posts by category.
     *
     * @param int $categoryId the category ID
     * @param int $page       the page number
     *
     * @return PaginationInterface the paginated posts
     */
    public function getPostsByCategory(int $categoryId, int $page): PaginationInterface;

    /**
     * Save a post.
     *
     * @param Post $post the post object to save
     */
    public function savePost(Post $post): void;

    /**
     * Delete a post.
     *
     * @param Post $post the post object to delete
     */
    public function deletePost(Post $post): void;

    /**
     * Get posts by tag.
     *
     * @param string $id   the tag ID
     * @param int    $page the page number
     *
     * @return PaginationInterface the paginated posts
     */
    public function getPostsByTag(int $id, int $page): PaginationInterface;

    /**
     * Prepare filters for post input.
     *
     * @param PostListInputFiltersDto $filters the filters to prepare
     *
     * @return array the prepared filters
     */
    public function prepareFilters(PostListInputFiltersDto $filters): PostListFiltersDto;
}
