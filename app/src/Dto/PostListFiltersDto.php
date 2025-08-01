<?php

/**
 * Post list filters DTO.
 */

namespace App\Dto;

use App\Entity\Category;
use App\Entity\Tag;

/**
 * Data Transfer Object for post list filters.
 */
class PostListFiltersDto
{
    /**
     * Constructor for PostListFiltersDto.
     *
     * @param Category|null $category the category to filter posts by
     * @param Tag|null      $tag      the tag to filter posts by
     * @param string|null   $search   the search term to filter posts by
     */
    public function __construct(public readonly ?Category $category = null, public readonly ?Tag $tag = null, public readonly ?string $search = null)
    {
    }
}
