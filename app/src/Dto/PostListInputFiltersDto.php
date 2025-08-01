<?php

/**
 * Post list input DTO.
 */

namespace App\Dto;

/**
 * Data Transfer Object for post list input filters.
 */
class PostListInputFiltersDto
{
    /**
     * Constructor for PostListInputFiltersDto.
     *
     * @param int|null    $categoryId the ID of the category to filter posts by
     * @param int|null    $tagId      the ID of the tag to filter posts by
     * @param string|null $search     the search term to filter posts by
     */
    public function __construct(public readonly ?int $categoryId = null, public readonly ?int $tagId = null, public readonly ?string $search = null)
    {
    }
}
