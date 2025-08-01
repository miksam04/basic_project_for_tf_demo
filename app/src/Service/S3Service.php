<?php
namespace App\Service;

use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class S3Service
{
    private $s3Client;
    private $bucket;

    public function __construct(S3Client $s3Client, string $bucket)
    {
        $this->s3Client = $s3Client;
        $this->bucket = $bucket;
    }

    public function uploadFile(UploadedFile $file, string $key): void
    {
        $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key'    => $key,
            'Body'   => fopen($file->getPathname(), 'rb'),
            'ACL'    => 'public-read',
        ]);
    }

    public function getFile(string $key): string
    {
        $result = $this->s3Client->getObject([
            'Bucket' => $this->bucket,
            'Key'    => $key,
        ]);
        return (string) $result['Body'];
    }

    public function deleteFile(string $key): void
    {
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucket,
            'Key'    => $key,
        ]);
    }
}
