<?php

/**
 * Tags data transformer.
 */

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use App\Interface\TagServiceInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class TagsDataTransformer.
 *
 * @implements DataTransformerInterface
 */
class TagsDataTransformer implements DataTransformerInterface
{
    /**
     * Constructor.
     *
     * @param TagServiceInterface $tagService The tag service
     */
    public function __construct(private readonly TagServiceInterface $tagService)
    {
    }

    /**
     * Transform a collection of Tag entities to a string of tag titles.
     *
     * @param Collection<Tag> $value The collection of Tag entities
     *
     * @return string A comma-separated string of tag titles
     */
    public function transform($value): string
    {
        if ($value->isEmpty()) {
            return '';
        }

        $tagTitles = [];

        foreach ($value as $tag) {
            $tagTitles[] = $tag->getTitle();
        }

        return implode(', ', $tagTitles);
    }

    /**
     * Reverse transform a string of tag titles to a collection of Tag entities.
     *
     * @param string $value A comma-separated string of tag titles
     *
     * @return array An array of Tag entities
     */
    public function reverseTransform($value): array
    {
        $tagTitles = explode(',', $value);

        $tags = [];

        foreach ($tagTitles as $tagTitle) {
            if ('' !== trim($tagTitle)) {
                $tag = $this->tagService->findOneByTitle(strtolower($tagTitle));
                if (!$tag instanceof Tag) {
                    $tag = new Tag();
                    $tag->setTitle($tagTitle);
                }
                $tags[] = $tag;
            }
        }

        return $tags;
    }
}
