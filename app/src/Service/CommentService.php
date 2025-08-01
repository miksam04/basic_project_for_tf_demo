<?php

/**
 * Comment service.
 */

namespace App\Service;

use App\Interface\CommentServiceInterface;
use App\Repository\CommentRepository;
use App\Entity\Comment;
use App\Entity\Post;

/**
 * Service responsible for managing comments.
 *
 * This service provides methods to save and retrieve comments.
 */
class CommentService implements CommentServiceInterface
{
    /**
     * Constructor.
     *
     * @param CommentRepository $commentRepository the repository for managing comments
     */
    public function __construct(private readonly CommentRepository $commentRepository)
    {
    }

    /**
     * Saves a comment.
     *
     * @param Comment $comment the comment to save
     *
     * @return void returns nothing
     */
    public function saveComment(Comment $comment): void
    {
        $this->commentRepository->saveComment($comment);
    }

    /**
     * Retrieves comments for a specific post.
     *
     * @param Post $post the post for which to retrieve comments
     *
     * @return Comment[] an array of comments associated with the post
     */
    public function getCommentsByPost(Post $post): array
    {
        return $this->commentRepository->findBy(['post' => $post]);
    }

    /**
     * Delete a comment.
     *
     * @param Comment $comment the comment to delete
     *
     * @return void returns nothing
     */
    public function deleteComment(Comment $comment): void
    {
        $this->commentRepository->remove($comment);
    }

    /**
     * Retrieves a comment by its ID.
     *
     * @param int $id the ID of the comment to retrieve
     *
     * @return Comment|null the comment if found, or null if not found
     */
    public function getCommentById(int $id): ?Comment
    {
        return $this->commentRepository->find($id);
    }
}
