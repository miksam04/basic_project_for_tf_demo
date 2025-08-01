<?php

/**
 * Category interface.
 */

namespace App\Interface;

use App\Entity\Category;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface CategoryServiceInterface.
 */
interface CategoryServiceInterface
{
    /**
     * Return paginated categories.
     *
     * @param int $page the page number
     *
     * @return PaginationInterface a pagination object containing Category objects
     */
    public function getPaginatedCategories(int $page): PaginationInterface;

    /**
     * Return all categories.
     *
     * @return Category[] an array of Category objects
     */
    public function getAllCategories(): array;

    /**
     * Return a category by its ID.
     *
     * @param int $id the category ID
     *
     * @return Category|null the category object or null if not found
     */
    public function getCategoryById(int $id): ?Category;

    /**
     * Save a category.
     *
     * @param Category $category the category to save
     */
    public function save(Category $category): void;

    /**
     * Delete a category.
     *
     * @param Category $category the category to delete
     */
    public function delete(Category $category): void;

    /**
     * Can the category be deleted?
     *
     * @param Category $category the category to check
     *
     * @return bool true if the category can be deleted, false otherwise
     */
    public function canBeDeleted(Category $category): bool;
}
