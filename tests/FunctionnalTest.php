<?php

namespace Test\Clamd;

use Clamd\Clamd;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class FunctionnalTest extends TestCase
{
    const EICAR = 'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*';

    /**
     * @var Clamd
     */
    private $clamd;
    /**
     * @var array
     */
    private $files;

    /**
     *
     */
    public static function setUpBeforeClass()
    {
        umask(0);
    }

    /**
     *
     */
    public function setUp()
    {
        if (isset($_SERVER['CLAM_TCP_ADDRESS']) && !empty($_SERVER['CLAM_TCP_ADDRESS'])) {
            $tcp = $_SERVER['CLAM_TCP_ADDRESS'];
        } else {
            $tcp = 'tcp://127.0.0.1:3310';
        }

        $this->clamd = new Clamd($tcp);

        $this->files['ok'] = $this->createFile('OK');
        $this->files['virus'] = $this->createFile(self::EICAR);
    }

    /**
     *
     */
    public function tearDown()
    {
        unlink($this->files['ok']);
        unlink($this->files['virus']);
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
        if (isset($_SERVER['CLAM_UNIX_ADDRESS']) && !empty($_SERVER['CLAM_UNIX_ADDRESS'])) {
            $socket = $_SERVER['CLAM_UNIX_ADDRESS'];
        } elseif (file_exists('/var/run/clamd.scan/clamd.sock')) {
            $socket = 'unix:///var/run/clamd.scan/clamd.sock';
        } else {
            $socket = 'unix:///var/run/clamav/clamd.ctl';
        }

        $clamd = new Clamd($socket);

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
        $result = $this->clamd->scanFile($this->files['virus']);

        $this->assertFalse($result);
        $this->assertSame('Eicar-Test-Signature', $this->clamd->getLastReason());
    }

    /**
     *
     */
    public function test_scan_ok()
    {
        $result = $this->clamd->scanFile($this->files['ok']);

        $this->assertTrue($result);
        $this->assertNull($this->clamd->getLastReason());
    }

    /**
     *
     */
    public function test_legacy_scan()
    {
        $result = $this->clamd->fileScan($this->files['ok']);

        $this->assertSame(Clamd::NO_VIRUS, $result['stats']);
    }

    /**
     * Create test file
     *
     * @param string $content
     *
     * @return string
     */
    private function createFile($content)
    {
        $name = tempnam(sys_get_temp_dir(), '');
        file_put_contents($name, $content);
        chmod($name, 0777);

        return $name;
    }
}
