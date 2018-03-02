<?php

namespace Clamd;

/**
 * ClamdInterface
 */
interface ClamdInterface
{
    /**
     * Ping clamd to see if we get a response.
     *
     * @return bool
     */
    public function ping();

    /**
     * Retrieve the running ClamAV version information.
     *
     * @return string
     */
    public function version();

    /**
     * Fetch stats for the ClamAV scan queue.
     *
     * @return array
     */
    public function stats();

    /**
     * Reload the ClamAV virus definition database.
     *
     * @return string
     */
    public function reload();

    /**
     * Shutdown clamd cleanly.
     *
     * @return string
     */
    public function shutdown();

    /**
     * Scan a single file.
     *
     * @param string $file The location of the file to scan.
     *
     * @return boolean
     */
    public function scanFile($file);

    /**
     * Scan a file or directory recursively using multiple threads.
     *
     * @param string $file The location of the file or directory to scan.
     *
     * @return boolean
     */
    public function multiscanFile($file);

    /**
     * Scan a file or directory recursively.
     *
     * @param string $file The location of the file or directory to scan.
     *
     * @return boolean
     */
    public function contScan($file);

    /**
     * Scan a local file via a stream.
     *
     * @param string $file The location of the file to scan.
     * @param int    $maxChunkSize The maximum chunk size in bytes to send to clamd at a time.
     *
     * @return boolean
     */
    public function scanLocalFile($file, $maxChunkSize = 1024);

    /**
     * Scan a stream.
     *
     * @param resource $stream A file stream
     * @param int      $maxChunkSize The maximum chunk size in bytes to send to clamd at a time.
     *
     * @return boolean
     */
    public function scanResourceStream($stream, $maxChunkSize = 1024);

    /**
     * Scan a stream.
     *
     * @param string $stream A file stream in string form.
     * @param int    $maxChunkSize The maximum chunk size in bytes to send to clamd at a time.
     *
     * @return boolean
     */
    public function scanStream($stream, $maxChunkSize = 1024);

    /**
     * Get the last scan reason
     *
     * @return string
     */
    public function getLastReason();

    /**
     * Start a session
     */
    public function startSession();

    /**
     * Start a session
     */
    public function endSession();
}
