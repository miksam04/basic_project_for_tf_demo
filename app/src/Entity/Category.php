<?php

/**
 * Category entity.
 */

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Category.
 *
 * Represents a category entity.
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories')]
#[ORM\UniqueConstraint(name: 'category_name_unique', columns: ['name'])]
#[UniqueEntity(fields: ['name'], message: 'category.name.in_use')]
class Category implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Type('string', message: 'category.name.type')]
    #[Assert\NotBlank(message: 'category.name.not_blank')]
    #[Assert\Length(min: 3, max: 64, minMessage: 'category.name.min_length', maxMessage: 'category.name.max_length')]
    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[Assert\Type('string', message: 'category.slug.type')]
    #[ORM\Column(length: 64)]
    #[Assert\Length(min: 3, max: 64)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;

    /**
     * Gets the ID of the category.
     *
     * @return int|null the ID of the category
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the name of the category.
     *
     * @return string|null the name of the category
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Sets the name of the category.
     *
     * @param string $name the name of the category
     *
     * @return static the current instance for method chaining
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the slug of the category.
     *
     * @return string|null the slug of the category
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Sets the slug of the category.
     *
     * @param string $slug the slug of the category
     *
     * @return static the current instance for method chaining
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Converts the category to a string representation.
     *
     * @return string the name of the category
     */
    public function __toString(): string
    {
        return (string) $this->name;
    }
}
