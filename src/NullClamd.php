<?php

namespace Clamd;

/**
 * Null object
 *
 * Allows application to disable clamd
 */
class NullClamd implements ClamdInterface
{
    /**
     * {@inheritdoc}
     */
    public function ping(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function version(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function stats(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function reload(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function scanFile($file): bool
    {
        return true;
    }

    /**
     * Alias of scanFile
     *
     * compatibility with the old interface
     *
     * @param string $file
     *
     * @return array
     */
    public function fileScan($file): array
    {
        $result = $this->createDummyScanResponse($file);

        $result['file'] = $result['filename'];
        $result['stats'] = $result['status'];

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function multiscanFile($file): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function contScan($file): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function scanLocalFile($file, $maxChunkSize = 1024): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function scanResourceStream($stream, $maxChunkSize = 1024): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function scanStream($stream, $maxChunkSize = 1024): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastReason(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function startSession(): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function endSession(): void
    {

    }

    /**
     * Create the dummy response for scan
     *
     * @param string $filename
     *
     * @return array
     */
    private function createDummyScanResponse($filename): array
    {
        return [
            'id' => '1',
            'filename' => $filename,
            'reason' => null,
            'status' => 'OK',
        ];
    }
}
