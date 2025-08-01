<?php

/**
 * PostListInputFiltersDto Resolver.
 */

namespace App\Resolver;

use App\Dto\PostListInputFiltersDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Resolver for PostListInputFiltersDto.
 */
class PostListInputFiltersDtoResolver implements ValueResolverInterface
{
    /**
     * Resolves the input filters for post list.
     *
     * @param Request          $request  the current request
     * @param ArgumentMetadata $argument the argument metadata
     *
     * @return iterable an iterable containing PostListInputFiltersDto
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_a($argumentType, PostListInputFiltersDto::class, true)) {
            return [];
        }

        $categoryId = $request->query->get('categoryId');
        $tagId = $request->query->get('tagId');
        $search = $request->query->get('search');

        return [new PostListInputFiltersDto($categoryId, $tagId, $search)];
    }
}
