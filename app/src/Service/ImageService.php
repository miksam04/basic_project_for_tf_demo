<?php

/**
 * Image service.
 */

namespace App\Service;

use App\Entity\Image;
use App\Entity\Post;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Interface\ImageServiceInterface;
use App\Interface\FileUploadServiceInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ImageService.
 *
 * This service handles the creation and management of image entities associated with posts.
 * It provides methods to create an image from an uploaded file and to remove the image file from the filesystem.
 */
class ImageService implements ImageServiceInterface
{
    /**
     * This constructor initializes the ImageService with the necessary dependencies.
     *
     * @param ImageRepository            $imageRepository   The repository for managing image entities
     * @param FileUploadServiceInterface $fileUploadService The service for handling file uploads
     * @param Filesystem                 $filesystem        The filesystem service for file operations
     */
    public function __construct(private readonly ImageRepository $imageRepository, private readonly FileUploadServiceInterface $fileUploadService, private readonly Filesystem $filesystem)
    {
    }

    /**
     * Create an image entity from an uploaded file and associate it with a post.
     *
     * @param UploadedFile $uploadedFile The uploaded image file
     * @param Post         $post         The post to associate the image with
     */
    public function create(UploadedFile $uploadedFile, Post $post): void
    {
        $image = new Image();

        $imageFilename = $this->fileUploadService->upload($uploadedFile);
        $image->setPost($post);
        $image->setFileName($imageFilename);
        $this->imageRepository->saveImage($image);
    }

    /**
     * Remove the image file from the filesystem.
     *
     * @param Image $image The image entity whose file should be removed
     */
    public function removeFile(Image $image): void
    {
        $filename = $image->getFileName();

        if (null !== $filename) {
            $this->filesystem->remove($this->fileUploadService->getTargetDirectory().'/'.$filename);
        }
    }
}
