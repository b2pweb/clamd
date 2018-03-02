<?php

namespace Test\Clamd;

use Clamd\NullClamd;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class NullClamdTest extends TestCase
{
    /**
     *
     */
    public function test_null_object()
    {
        $clamd = new NullClamd();

        $this->assertSame(true, $clamd->ping());
        $this->assertSame(null, $clamd->version());
        $this->assertSame([], $clamd->stats());
        $this->assertSame(true, $clamd->reload());
        $this->assertSame(true, $clamd->shutdown());
        $this->assertSame(true, $clamd->disconnect());
        $this->assertSame(null, $clamd->getLastReason());
        $this->assertSame(null, $clamd->startSession());
        $this->assertSame(null, $clamd->endSession());
    }

    /**
     *
     */
    public function test_legacy_scan()
    {
        $clamd = new NullClamd();

        $result = [
            'id' => '1',
            'filename' => 'foo',
            'reason' => null,
            'status' => 'OK',
            'file' => 'foo',
            'stats' => 'OK',
        ];

        $this->assertSame($result, $clamd->fileScan('foo'));
    }

    /**
     *
     */
    public function test_scan()
    {
        $clamd = new NullClamd();

        $result = [
            'id' => '1',
            'filename' => 'foo',
            'reason' => null,
            'status' => 'OK',
        ];

        $this->assertSame($result, $clamd->scanFile('foo'));
        $this->assertSame($result, $clamd->multiscanFile('foo'));
        $this->assertSame($result, $clamd->contScan('foo'));
        $this->assertSame($result, $clamd->scanLocalFile('foo'));
    }

    /**
     *
     */
    public function test_scan_stream()
    {
        $clamd = new NullClamd();

        $result = [
            'id' => '1',
            'filename' => null,
            'reason' => null,
            'status' => 'OK',
        ];

        $this->assertSame($result, $clamd->scanResourceStream('stream'));
        $this->assertSame($result, $clamd->scanStream('stream'));
    }
}