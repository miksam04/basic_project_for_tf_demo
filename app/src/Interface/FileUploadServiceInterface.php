<?php

/**
 * File upload service interface.
 */

namespace App\Interface;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface FileUploadServiceInterface.
 */
interface FileUploadServiceInterface
{
    /**
     * Upload a file and return the file name.
     *
     * @param UploadedFile $file The file to upload
     *
     * @return string The name of the uploaded file
     */
    public function upload(UploadedFile $file): string;

    /**
     * Get the target directory where files are uploaded.
     *
     * @return string The target directory path
     */
    public function getTargetDirectory(): string;
}
