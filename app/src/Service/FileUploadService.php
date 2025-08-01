<?php

/**
 * File upload service.
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Interface\FileUploadServiceInterface;
use League\Flysystem\FilesystemOperator;

/**
 * Class FileUploadService.
 *
 * This service handles file uploads, generating a safe file name and uploading the file to S3 using Flysystem.
 */
class FileUploadService implements FileUploadServiceInterface
{
    private FilesystemOperator $storage;
    private SluggerInterface $slugger;

    public function __construct(FilesystemOperator $storage, SluggerInterface $slugger)
    {
        $this->storage = $storage;
        $this->slugger = $slugger;
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
            $stream = fopen($file->getPathname(), 'r');
            if ($stream === false) {
                throw new FileException('Could not open file for reading.');
            }
            $this->storage->writeStream($fileName, $stream);
            fclose($stream);
        } catch (\Throwable $e) {
            throw new FileException('Could not upload the file to S3: ' . $e->getMessage());
        }

        return $fileName;
    }

    /**
     * Get the target directory where files are uploaded (for compatibility, returns bucket prefix or empty string).
     *
     * @return string
     */
    public function getTargetDirectory(): string
    {
        return '';
    }

    /**
     * Delete a file from S3 storage.
     *
     * @param string $fileName
     * @return void
     */
    public function delete(string $fileName): void
    {
        try {
            $this->storage->delete($fileName);
        } catch (\Throwable $e) {
            throw new FileException('Could not delete the file from S3: ' . $e->getMessage());
        }
    }

    /**
     * Download a file from S3 storage (returns file contents as string).
     *
     * @param string $fileName
     * @return string
     */
    public function download(string $fileName): string
    {
        try {
            return $this->storage->read($fileName);
        } catch (\Throwable $e) {
            throw new FileException('Could not download the file from S3: ' . $e->getMessage());
        }
    }

    /**
     * Replace (edit) a file in S3 storage.
     *
     * @param string $fileName
     * @param UploadedFile $file
     * @return void
     */
    public function replace(string $fileName, UploadedFile $file): void
    {
        try {
            $stream = fopen($file->getPathname(), 'r');
            if ($stream === false) {
                throw new FileException('Could not open file for reading.');
            }
            $this->storage->delete($fileName);
            $this->storage->writeStream($fileName, $stream);
            fclose($stream);
        } catch (\Throwable $e) {
            throw new FileException('Could not replace the file in S3: ' . $e->getMessage());
        }
    }
}
