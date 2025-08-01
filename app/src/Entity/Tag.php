<?php

/**
 * Tag entity.
 */

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Represents a tag entity.
 */
#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\UniqueConstraint(name: 'tag_title_unique', columns: ['title'])]
#[UniqueEntity(fields: ['title'], message: 'tag.title.in_use')]
#[ORM\Table(name: 'tags')]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'tag.title.not_blank')]
    #[Assert\Type('string', message: 'tag.title.type_error')]
    #[Assert\Length(min: 3, max: 64, minMessage: 'tag.title.min_length', maxMessage: 'tag.title.max_length')]
    #[ORM\Column(length: 64)]
    private ?string $title = null;

    #[Assert\Type('string')]
    #[Gedmo\Slug(fields: ['title'])]
    #[Assert\Length(min: 3, max: 64)]
    #[ORM\Column(length: 64)]
    private ?string $slug = null;

    /**
     * Gets the ID of the tag.
     *
     * @return int|null the ID of the tag
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the title of the tag.
     *
     * @return string|null the title of the tag
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of the tag.
     *
     * @param string $title the title to set
     *
     * @return static the current instance for method chaining
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the slug of the tag.
     *
     * @return string|null the slug of the tag
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Sets the slug of the tag.
     *
     * @param string $slug the slug to set
     *
     * @return static the current instance for method chaining
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
