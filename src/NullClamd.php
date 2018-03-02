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
    public function ping()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function version()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function stats()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function reload()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function scanFile($file)
    {
        return $this->createDummyScanResponse($file);
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
    public function fileScan($file)
    {
        $result = $this->createDummyScanResponse($file);

        $result['file'] = $result['filename'];
        $result['stats'] = $result['status'];

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function multiscanFile($file)
    {
        return $this->createDummyScanResponse($file);
    }

    /**
     * {@inheritdoc}
     */
    public function contScan($file)
    {
        return $this->createDummyScanResponse($file);
    }

    /**
     * {@inheritdoc}
     */
    public function scanLocalFile($file, $maxChunkSize = 1024)
    {
        return $this->createDummyScanResponse($file);
    }

    /**
     * {@inheritdoc}
     */
    public function scanResourceStream($stream, $maxChunkSize = 1024)
    {
        return $this->createDummyScanResponse(null);
    }

    /**
     * {@inheritdoc}
     */
    public function scanStream($stream, $maxChunkSize = 1024)
    {
        return $this->createDummyScanResponse(null);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastReason()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function startSession()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function endSession()
    {

    }

    /**
     * Create the dummy response for scan
     *
     * @param string $filename
     *
     * @return array
     */
    private function createDummyScanResponse($filename)
    {
        return [
            'id' => '1',
            'filename' => $filename,
            'reason' => null,
            'status' => 'OK',
        ];
    }
}
