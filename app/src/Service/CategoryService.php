<?php

/**
 * Category service.
 */

namespace App\Service;

use App\Interface\CategoryServiceInterface;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Entity\Category;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Service responsible for managing categories.
 *
 * This service handles operations such as retrieving all categories,
 * fetching categories by their ID, and other business logic related
 * to categories.
 */
class CategoryService implements CategoryServiceInterface
{
    public $postRepository;
    public $categoryRepository;
    public $paginator;
    private const ITEMS_PER_PAGE = 10;

    /**
     * CategoryService constructor.
     *
     * @param CategoryRepository $categoryRepository The category repository
     * @param PaginatorInterface $paginator          The paginator service
     * @param PostRepository     $postRepository     The post repository
     */
    public function __construct(CategoryRepository $categoryRepository, PaginatorInterface $paginator, PostRepository $postRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
    }

    /**
     * Get all categories.
     *
     * @param int $page The page number
     *
     * @return PaginationInterface A pagination object containing Category objects
     */
    public function getPaginatedCategories(int $page): PaginationInterface
    {
        $query = $this->categoryRepository->queryAll();

        return $this->paginator->paginate($query, $page, self::ITEMS_PER_PAGE);
    }

    /**
     * Get all categories.
     *
     * @return array An array of Category objects
     */
    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * Get a category by its ID.
     *
     * @param int $id The category ID
     *
     * @return Category|null The Category object or null if not found
     */
    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * Save a category.
     *
     * @param Category $category The category to save
     */
    public function save(Category $category): void
    {
        $this->categoryRepository->save($category);
    }

    /**
     * Delete a category.
     *
     * @param Category $category The category to delete
     *
     * @return void return nothing
     */
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    /**
     * Check if a category can be deleted.
     *
     * A category can be deleted if it has no associated posts.
     *
     * @param Category $category The category to check
     *
     * @return bool True if the category can be deleted, false otherwise
     */
    public function canBeDeleted(Category $category): bool
    {
        try {
            $result = $this->postRepository->countByCategory($category);

            return $result <= 0;
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
}
