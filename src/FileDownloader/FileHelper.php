<?php

declare(strict_types = 1);

namespace App\FileDownloader;

readonly class FileHelper
{
    private const array IMAGE_EXTENSIONS = [
        'bmp'  => 'bmp',
        'gif'  => 'gif',
        'heic' => 'heic',
        'jpeg' => 'jpg',
        'jpg'  => 'jpg',
        'png'  => 'png',
        'tiff' => 'tiff',
        'webp' => 'webp',
    ];

    final public const array IMAGE_MIMETYPES = [
        'image/gif'      => 'gif',
        'image/heic'     => 'heic',
        'image/jpeg'     => 'jpg',
        'image/png'      => 'png',
        'image/tiff'     => 'tiff',
        'image/webp'     => 'webp',
        'image/x-ms-bmp' => 'bmp',

        // invalid mime-type/extension combinations, but who knows?
        'image/jpg' => 'jpg',
    ];

    private const array DOCUMENT_EXTENSIONS = [
        'pdf' => 'pdf',
    ];

    private const array DOCUMENT_MIMETYPES = [
        'application/pdf' => 'pdf',
    ];

    private static function getExtension(array $extensions, string $pathOrExtension): ?string
    {
        foreach ($extensions as $extension => $normalized) {
            if ($pathOrExtension === $extension
                || str_ends_with($pathOrExtension, '.' . $extension)
            ) {
                return $normalized;
            }
        }

        return null;
    }

    public static function mimeTypeIsImage(string $mimeType): bool
    {
        return !empty(self::IMAGE_MIMETYPES[$mimeType]);
    }

    public static function mimeTypeIsDocument(string $mimeType): bool
    {
        return !empty(self::DOCUMENT_MIMETYPES[$mimeType]);
    }

    public static function mimeTypeIsStorable(string $mimeType): bool
    {
        return self::mimeTypeIsImage($mimeType)
            || self::mimeTypeIsDocument($mimeType);
    }

    public static function extensionIsImage(string $pathOrExtension): bool
    {
        return (bool) self::getExtension(self::IMAGE_EXTENSIONS, $pathOrExtension);
    }

    public static function extensionIsDocument(string $pathOrExtension): bool
    {
        return (bool) self::getExtension(self::DOCUMENT_EXTENSIONS, $pathOrExtension);
    }

    public static function extensionIsStorable(string $pathOrExtension): bool
    {
        return self::extensionIsImage($pathOrExtension)
            || self::extensionIsDocument($pathOrExtension);
    }
}
