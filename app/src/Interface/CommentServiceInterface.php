<?php

/**
 * Comment service interface.
 */

namespace App\Interface;

use App\Entity\Comment;
use App\Entity\Post;

/**
 * Interface CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Get comments by post ID.
     *
     * @param Post $post the post object for which to retrieve comments
     *
     * @return array Returns an array of Comment objects
     */
    public function getCommentsByPost(Post $post): array;

    /**
     * Save a comment.
     *
     * @param Comment $comment the comment object to save
     *
     * @return void Return nothing
     */
    public function saveComment(Comment $comment): void;

    /**
     * Remove a comment.
     *
     * @param Comment $comment the comment object to remove
     *
     * @return void Return nothing
     */
    public function deleteComment(Comment $comment): void;

    /**
     * Get a comment by its ID.
     *
     * @param int $id the ID of the comment to retrieve
     *
     * @return Comment|null Returns the Comment object if found, or null if not found
     */
    public function getCommentById(int $id): ?Comment;
}
