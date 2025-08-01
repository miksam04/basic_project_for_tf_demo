<?php

/**
 * Image service interface.
 */

namespace App\Interface;

use App\Entity\Image;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface ImageServiceInterface.
 */
interface ImageServiceInterface
{
    /**
     * Create an image entity from an uploaded file and associate it with a post.
     *
     * @param UploadedFile $uploadedFile The uploaded image file
     * @param Post         $post         The post to associate the image with
     */
    public function create(UploadedFile $uploadedFile, Post $post): void;

    /**
     * Remove the file associated with the image entity.
     *
     * @param Image $image The image entity to remove
     */
    public function removeFile(Image $image): void;
}
