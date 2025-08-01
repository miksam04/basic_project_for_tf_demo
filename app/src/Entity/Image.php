<?php

/**
 * Image entity.
 *
 * This entity represents an image associated with a post.
 */

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Image.
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Table(name: 'images')]
#[ORM\UniqueConstraint(name: 'uq_images_file_name', columns: ['file_name'])]
#[UniqueEntity(fields: ['file_name'], message: 'image.file_name.in_use')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'images', cascade:['persist', 'remove'], fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'image.post.not_null')]
    private ?Post $post = null;

    #[ORM\Column(length: 191)]
    #[Assert\Type('string')]
    private ?string $fileName = null;

    /**
     * Get the ID of the image.
     *
     * @return int|null The ID of the image or null if not set
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the post associated with the image.
     *
     * @return Post|null The post associated with the image or null if not set
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * Set the post associated with the image.
     *
     * @param Post|null $post The post to associate with the image
     *
     * @return static Returns the current instance for method chaining
     */
    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get the file name of the image.
     *
     * @return string|null The file name of the image or null if not set
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * Set the file name of the image.
     *
     * @param string $fileName The file name to set
     *
     * @return static Returns the current instance for method chaining
     */
    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }
}
