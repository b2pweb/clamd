<?php

namespace Clamd;

use Socket\Raw\Factory as SocketFactory;
use Xenolope\Quahog\Client;
use Xenolope\Quahog\Exception\ConnectionException;
use Xenolope\Quahog\Result;

/**
 * Clamd
 */
class Clamd implements ClamdInterface
{
    const NO_VIRUS = 'OK';

    /**
     * The connection dsn
     *
     * @var string
     */
    private $dsn;

    /**
     * The conenction timeout
     *
     * @var int
     */
    private $timeout;

    /**
     * The instance of client used by session
     *
     * @var Client
     */
    private $client;

    /**
     * The last reason
     *
     * @var string
     */
    private $lastReason;

    /**
     * Clamd.
     *
     * @param string $dsn
     * @param int $timeout
     */
    public function __construct($dsn, $timeout = 30)
    {
        $this->dsn = $dsn;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function ping()
    {
        try {
            return $this->client()->ping();
        } catch (ConnectionException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function version()
    {
        return $this->client()->version();
    }

    /**
     * {@inheritdoc}
     */
    public function stats()
    {
        $stats = [];

        foreach (explode("\n", $this->client()->stats()) as $line) {
            $parts = explode(':', $line);

            if (!isset($parts[1])) {
                continue;
            }

            $stats[trim($parts[0])] = trim($parts[1]);
        }

        return $stats;
    }

    /**
     * {@inheritdoc}
     */
    public function reload()
    {
        return $this->client()->reload() === 'RELOADING';
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        return $this->client()->shutdown() === '';
    }

    /**
     * {@inheritdoc}
     */
    public function scanFile($file)
    {
        return $this->isFileOk(
            $this->client()->scanFile($file)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function multiscanFile($file)
    {
        return $this->isFileOk(
            $this->client()->multiscanFile($file)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function contScan($file)
    {
        return $this->isFileOk(
            $this->client()->contScan($file)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function scanLocalFile($file, $maxChunkSize = 1024)
    {
        return $this->isFileOk(
            $this->client()->scanLocalFile($file, $maxChunkSize)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function scanResourceStream($stream, $maxChunkSize = 1024)
    {
        return $this->isFileOk(
            $this->client()->scanResourceStream($stream, $maxChunkSize)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function scanStream($stream, $maxChunkSize = 1024)
    {
        return $this->isFileOk(
            $this->client()->scanStream($stream, $maxChunkSize)
        );
    }

    /**
     * Gets the raw result of a scan
     *
     * Legacy method
     *
     * @deprecated Will be rename to rawScan
     *
     * @param string $file
     *
     * @return array
     */
    public function fileScan($file)
    {
        $result = $this->client()->scanFile($file);

        if ($result instanceof Result) {
            $legacyResult = [
                'filename' => $result->getFilename(),
                'reason' => $result->getReason(),
                'id' => $result->getReason(),
            ];

            if ($result->isOk()) {
                $legacyResult['status'] = 'OK';
            } elseif ($result->isFound()) {
                $legacyResult['status'] = 'FOUND';
            } else {
                $legacyResult['status'] = 'ERROR';
            }

            $result = $legacyResult;
        }

        $result['file'] = $result['filename'];
        $result['stats'] = $result['status'];

        $this->isFileOk($result);

        return $result;
    }

    /**
     * Check if the scan is ok
     *
     * @param array|Result $scanResult
     *
     * @return boolean
     */
    private function isFileOk($scanResult)
    {
        if ($scanResult instanceof Result) {
            $this->lastReason = $scanResult->getReason();

            return $scanResult->isOk();
        }

        $this->lastReason = $scanResult['reason'];

        return $scanResult['status'] === self::NO_VIRUS;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastReason()
    {
        return $this->lastReason;
    }

    /**
     * {@inheritdoc}
     */
    public function startSession()
    {
        $this->client = $this->client();
    }

    /**
     * {@inheritdoc}
     */
    public function endSession()
    {
        $this->client = null;
    }

    /**
     * Set the clamd client
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get the clamd client
     *
     * @return Client
     */
    public function client()
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $factory = new SocketFactory();

        return new Client($factory->createClient($this->dsn), $this->timeout, PHP_NORMAL_READ);
    }
}
