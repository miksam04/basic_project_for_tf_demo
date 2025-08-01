<?php

/**
 * Post service.
 */

namespace App\Service;

use App\Dto\PostListInputFiltersDto;
use App\Dto\PostListFiltersDto;
use App\Interface\PostServiceInterface;
use App\Interface\CategoryServiceInterface;
use App\Interface\TagServiceInterface;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Entity\Post;
use App\Entity\User;

/**
 * Service responsible for managing blog posts.
 *
 * This service provides methods to retrieve, paginate,
 * and filter posts by categories or other criteria.
 */
class PostService implements PostServiceInterface
{
    private const ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param PostRepository           $postRepository  The post repository
     * @param PaginatorInterface       $paginator       The paginator service
     * @param UserRepository           $userRepository  The user repository
     * @param CategoryServiceInterface $categoryService The category service
     * @param TagServiceInterface      $tagService      The tag service
     */
    public function __construct(private readonly PostRepository $postRepository, private readonly PaginatorInterface $paginator, private readonly UserRepository $userRepository, private readonly CategoryServiceInterface $categoryService, private readonly TagServiceInterface $tagService)
    {
    }

    /**
     * Get paginated posts.
     *
     * @param int                     $page    The page number
     * @param User|null               $author  The author of the posts or null if not filtering by author
     * @param PostListInputFiltersDto $filters The input filters for the post list
     *
     * @return PaginationInterface The paginated posts
     */
    public function getPaginatedPosts(int $page, ?User $author, PostListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->postRepository->queryAll($author, $filters),
            $page,
            self::ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['ppost.id', 'post.createdAt', 'post.updatedAt', 'post.title', 'category.title'],
                'defaultSortFieldName' => 'post.updatedAt',
                'defaultSortDirection' => 'DESC',
            ]
        );
    }

    /**
     * Get posts by id.
     *
     * @param int $id The post id
     *
     * @return Post|null The post entity or null if not found
     */
    public function getPostById(int $id): ?Post
    {
        return $this->postRepository->find($id);
    }

    /**
     * Get posts by category.
     *
     * @param int $categoryId The category id
     * @param int $page       The page number
     *
     * @return PaginationInterface The paginated posts
     */
    public function getPostsByCategory(int $categoryId, int $page): PaginationInterface
    {
        $query = $this->postRepository->queryByCategory($categoryId);

        return $this->paginator->paginate($query, $page, self::ITEMS_PER_PAGE);
    }

    /**
     * Save a post.
     *
     * @param Post $post The post to save
     *
     * @return void returns nothing
     */
    public function savePost(Post $post): void
    {
        $this->postRepository->savePost($post);
    }

    /**
     * Delete a post.
     *
     * @param Post $post The post to delete
     *
     * @return void returns nothing
     */
    public function deletePost(Post $post): void
    {
        $this->postRepository->deletePost($post);
    }

    /**
     * Get posts by tag.
     *
     * @param int $id   The tag id
     * @param int $page The page number
     *
     * @return PaginationInterface The paginated posts
     */
    public function getPostsByTag(int $id, int $page): PaginationInterface
    {
        $query = $this->postRepository->queryByTag($id);

        return $this->paginator->paginate($query, $page, self::ITEMS_PER_PAGE);
    }

    /**
     * Get posts by filters.
     *
     * @param PostListInputFiltersDto $filters The input filters
     *
     * @return PaginationInterface The paginated posts
     */
    public function prepareFilters(PostListInputFiltersDto $filters): PostListFiltersDto
    {
        return new PostListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->getCategoryById($filters->categoryId) : null,
            null !== $filters->tagId ? $this->tagService->getTagById($filters->tagId) : null,
            $filters->search ?? null
        );
    }
}
