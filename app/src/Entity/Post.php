<?php

/**
 * Post entity.
 */

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Post.
 *
 * Represents a post entity.
 */
#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: 'posts')]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'post.title.not_blank')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'post.title.min_length', maxMessage: 'post.title.max_length')]
    #[Assert\Type('string', message: 'post.title.type_error')]
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'post.content.not_blank')]
    #[Assert\Length(min: 3, max: 65535, minMessage: 'post.content.min_length', maxMessage: 'post.content.max_length')]
    #[Assert\Type('string', message: 'post.content.type_error')]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Assert\Type(\DateTimeImmutable::class, message: 'post.created_at.type_error')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'update')]
    #[Assert\Type(\DateTimeImmutable::class, message: 'post.updated_at.type_error')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'post.author.not_null')]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'post.category.not_null')]
    #[Assert\Type(Category::class, message: 'post.category.type_error')]
    private ?Category $category = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, fetch: 'EXTRA_LAZY', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'post_tags')]
    #[Assert\Valid]
    private Collection $tags;

    #[ORM\Column(type: 'string', length: 20, options: ['default' => 'draft'])]
    #[Assert\NotBlank(message: 'post.status.not_blank')]
    #[Assert\Choice(choices: ['draft', 'published'], message: 'post.status.invalid_choice')]
    private string $status = 'draft';

    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'post', cascade: ['persist', 'remove'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $images;

    /**
     * Post constructor.
     *
     * Initializes the comments collection.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * Gets the ID of the post.
     *
     * @return int|null the ID of the post
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the title of the post.
     *
     * @return string|null the title of the post
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of the post.
     *
     * @param string $title the title of the post
     *
     * @return static the current instance for method chaining
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the content of the post.
     *
     * @return string|null the content of the post
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Sets the content of the post.
     *
     * @param string $content the content of the post
     *
     * @return static the current instance for method chaining
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the creation date and time of the post.
     *
     * @return \DateTimeImmutable|null the creation date and time of the post
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date and time of the post.
     *
     * @param \DateTimeImmutable $createdAt the creation date and time of the post
     *
     * @return static the current instance for method chaining
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Gets the last updated date and time of the post.
     *
     * @return \DateTime|null the last updated date and time of the post
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Sets the last updated date and time of the post.
     *
     * @param \DateTime|null $updatedAt the last updated date and time of the post
     *
     * @return static the current instance for method chaining
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Gets the author of the post.
     *
     * @return User|null the author of the post
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Sets the author of the post.
     *
     * @param User|null $author the author of the post
     *
     * @return static the current instance for method chaining
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Gets the category of the post.
     *
     * @return Category|null the category of the post
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Sets the category of the post.
     *
     * @param Category|null $category the category of the post
     *
     * @return static the current instance for method chaining
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Gets the comments associated with the post.
     *
     * @return Collection the collection of comments
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Sets the tags associated with the post.
     *
     * @param Collection<int, Tag> $tag the collection of tags
     *
     * @return static the current instance for method chaining
     */
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * Removes a tag from the post.
     *
     * @param Tag $tag the tag to remove
     *
     * @return static the current instance for method chaining
     */
    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * Gets the status of the post.
     *
     * @return string the status of the post
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Sets the status of the post.
     *
     * @param string $status the status of the post
     *
     * @return self the current instance for method chaining
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Gets the images associated with the post.
     *
     * @return Collection the collection of images
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * Removes an image from the post.
     *
     * @param Image $image the image to remove
     *
     * @return self the current instance for method chaining
     */
    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            if ($image->getPost() === $this) {
                $image->setPost(null);
            }
        }

        return $this;
    }
}
