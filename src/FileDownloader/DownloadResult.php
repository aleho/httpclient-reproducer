<?php

declare(strict_types = 1);

namespace App\FileDownloader;

readonly class DownloadResult
{
    public function __construct(
        public bool $validAsset,
        public ?string $contentType,
        public ?int $errorCode = null,
    ) {
    }
}
