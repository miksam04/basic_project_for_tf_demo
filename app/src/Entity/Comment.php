<?php

/**
 * Comment entity.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a comment on a post.
 *
 * This entity is used to store comments made by users on posts.
 * It includes fields for the comment's email, nickname, content,
 * creation date, and the associated post.
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'comments')]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'form.email.not_blank')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'form.email.min_length', maxMessage: 'form.email.max_length')]
    #[Assert\Email(message: 'form.email.invalid')]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'form.nickname.not_blank')]
    #[Assert\Length(min: 3, max: 64, minMessage: 'form.nickname.min_length', maxMessage: 'form.nickname.max_length')]
    #[ORM\Column(length: 64)]
    private ?string $nickname = null;

    #[Assert\NotBlank(message: 'form.content.not_blank')]
    #[Assert\Length(min: 2, max: 512, minMessage: 'form.content.min_length', maxMessage: 'form.content.max_length')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    /**
     * Constructor.
     *
     * Initializes the createdAt field to the current date and time.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Returns the ID of the comment.
     *
     * @return int|null the ID of the comment, or null if not set
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the email of the comment.
     *
     * @return string|null the email of the comment, or null if not set
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the email of the comment.
     *
     * @param string $email the email to set
     *
     * @return static returns the current instance for method chaining
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Returns the nickname of the comment.
     *
     * @return string|null the nickname of the comment, or null if not set
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * Sets the nickname of the comment.
     *
     * @param string $nickname the nickname to set
     *
     * @return static returns the current instance for method chaining
     */
    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Returns the content of the comment.
     *
     * @return string|null the content of the comment, or null if not set
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Sets the content of the comment.
     *
     * @param string $content the content to set
     *
     * @return static returns the current instance for method chaining
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Returns the creation date of the comment.
     *
     * @return \DateTimeImmutable|null the creation date of the comment, or null if not set
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date of the comment.
     *
     * @param \DateTimeImmutable $createdAt the creation date to set
     *
     * @return static returns the current instance for method chaining
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns the post associated with the comment.
     *
     * @return Post|null the post associated with the comment, or null if not set
     */
    public function getPost(): ?Post
    {
        return $this->post;
    }

    /**
     * Sets the post associated with the comment.
     *
     * @param Post|null $post the post to associate with the comment
     *
     * @return static returns the current instance for method chaining
     */
    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }
}
