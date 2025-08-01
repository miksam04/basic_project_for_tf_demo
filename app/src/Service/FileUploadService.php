<?php

/**
 * File upload service.
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Interface\FileUploadServiceInterface;

/**
 * Class FileUploadService.
 *
 * This service handles file uploads, generating a safe file name and moving the file to the target directory.
 */
class FileUploadService implements FileUploadServiceInterface
{
    /**
     * Constructor.
     *
     * @param string           $targetDirectory The directory where files will be uploaded
     * @param SluggerInterface $slugger         The slugger service to generate safe file names
     */
    public function __construct(private readonly string $targetDirectory, private readonly SluggerInterface $slugger)
    {
    }

    /**
     * Upload a file and return the file name.
     *
     * @param UploadedFile $file The file to upload
     *
     * @return string The name of the uploaded file
     */
    public function upload(UploadedFile $file): string
    {
        $extension = $file->guessExtension();
        $fileName = '';

        if (null !== $extension) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename.'-'.uniqid().'.'.$extension;
        }

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException) {
            // Handle exception if something happens during file upload
            throw new FileException('Could not upload the file.');
        }

        return $fileName;
    }

    /**
     * Get the target directory where files are uploaded.
     *
     * @return string The target directory path
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
