<?php

namespace Test\Clamd;

use Clamd\Clamd;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class FunctionnalTest extends TestCase
{
    /**
     * @var Clamd
     */
    private $clamd;

    /**
     *
     */
    public function setUp()
    {
        $this->clamd = new Clamd('127.0.0.1:3310');
    }

    /**
     *
     */
    public function test_tcp()
    {
        $this->assertSame(true, $this->clamd->ping());
    }

    /**
     *
     */
    public function test_pipe()
    {
        $clamd = new Clamd('unix:///var/run/clamd.scan/clamd.sock');

        $this->assertSame(true, $clamd->ping());
    }

    /**
     *
     */
    public function test_version()
    {
        $this->assertContains('ClamAV', $this->clamd->version());
    }

    /**
     *
     */
    public function test_multiple_calls()
    {
        $this->clamd->version();
        $this->clamd->version();
        $this->clamd->version();
        $this->clamd->version();

        $this->assertContains('ClamAV', $this->clamd->version());
    }

    /**
     *
     */
    public function test_stats()
    {
        $stats = $this->clamd->stats();

        $this->assertEquals(1, $stats['POOLS']);
    }

    /**
     *
     */
    public function test_scan_virus()
    {
        $result = $this->clamd->scanFile(__DIR__.'/_file/file_infected.pdf');

        $this->assertFalse($result);
        $this->assertSame('Clamav.Test.File-6', $this->clamd->getLastReason());
    }

    /**
     *
     */
    public function test_scan_ok()
    {
        $result = $this->clamd->scanFile(__DIR__.'/_file/image.jpg');

        $this->assertTrue($result);
        $this->assertNull($this->clamd->getLastReason());
    }

    /**
     *
     */
    public function test_legacy_scan()
    {
        $result = $this->clamd->fileScan(__DIR__.'/_file/image.jpg');

        $this->assertSame(Clamd::NO_VIRUS, $result['stats']);
    }
}
