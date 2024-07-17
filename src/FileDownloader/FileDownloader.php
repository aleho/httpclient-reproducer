<?php

declare(strict_types = 1);

namespace App\FileDownloader;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\HttpClient;

readonly class FileDownloader
{
    public function __construct(
        protected HttpClient\HttpClientInterface $httpClient,
        protected LoggerInterface $logger,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function download(string $url, File $targetFile): DownloadResult
    {
        $response = $this->httpClient->request('GET', $url, [
            'verify_peer' => false,
            'verify_host' => false,
        ]);

        if (($statusCode = $response->getStatusCode()) >= 300) {
            $this->logger->debug('Invalid response getting {url}: {statusCode}', [
                'url'        => $url,
                'statusCode' => $statusCode,
                'buffer'     => false,
            ]);

            return new DownloadResult(false, null, $statusCode);
        }

        $headers     = $response->getHeaders();
        $contentType = $headers['content-type'][0] ?? null;

        if ($contentType) {
            $contentType = explode(';', $contentType, 2)[0] ?? null;
        }

        $this->logger->debug('Got {contentType} for {url}', [
            'contentType' => $contentType,
            'url'         => $url,
        ]);

        if (!$this->isDownloadableUrl($url, $contentType)) {
            $this->logger->debug('Not downloading {url}', [
                'url'         => $url,
                'contentType' => $contentType,
            ]);

            return new DownloadResult(false, $contentType);
        }

        $this->logger->debug('Downloading file {url} to {file}', [
            'url'  => $url,
            'file' => $targetFile->getPathname(),
        ]);

        $fileHandler = $targetFile->openFile('wb');
        foreach ($this->httpClient->stream($response) as $chunk) {
            $fileHandler->fwrite($chunk->getContent());
        }

        clearstatcache();

        $isValid = $targetFile->getSize() > 0;

        if ($isValid) {
            $contentType = mime_content_type($targetFile->getPathname());

            if (!$contentType || !FileHelper::mimeTypeIsStorable($contentType)) {
                $isValid = false;
            }
        }

        return new DownloadResult($isValid, $contentType);
    }

    protected function isValidMimeType(?string $mimeType): bool
    {
        return ($mimeType === 'application/save'
            || ($mimeType && FileHelper::mimeTypeIsStorable($mimeType))
        );
    }

    protected function isDownloadableUrl(string $url, ?string $mimeType): bool
    {
        if ($this->isValidMimeType($mimeType)) {
            // always trust the mime-type
            return true;
        }

        // fallback to extension guessing because server didn't provide a mime-type
        $parsedPath = parse_url($url, PHP_URL_PATH);

        if (!$parsedPath) {
            $this->logger->debug('URL {url} has no path component', [
                'url' => $url,
            ]);

            return false;
        }

        return FileHelper::extensionIsStorable($parsedPath);
    }

    /* HINT
     * Comment the (never executed) function below and try again if you're
     * unable to reproduce the issue reliably.
     */

    public function uselessCodeTriggeringRecompilation(): void
    {
        $this->logger->error('This is useless');
    }
}
