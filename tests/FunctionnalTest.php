<?php

namespace Test\Clamd;

use Bdf\Config\Config;
use Bdf\Web\Application;
use Clamd\Clamd;
use Clamd\NullClamd;
use Clamd\ServiceProvider\ClamdServiceProvider;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class FunctionnalTest extends TestCase
{
    /**
     * @var Application
     */
    private $container;

    /**
     * @var Clamd
     */
    private $clamd;

    /**
     *
     */
    public function setUp()
    {
        $this->container = new Application([
            'config' => new Config([
                'clamd' => [
                    'enable' => true,
                    'dsn' => '127.0.0.1:3310',
                ]
            ])
        ]);

        $provider = new ClamdServiceProvider();
        $provider->configure($this->container);
    }

    /**
     *
     */
    public function test_tcp()
    {
        $this->clamd = $this->container->get('clamd');

        $this->assertSame(true, $this->clamd->ping());
    }

    /**
     *
     */
    public function test_pipe()
    {
        $this->container->config()->set('clamd.dsn', 'unix:///var/run/clamd.scan/clamd.sock');
        $this->clamd = $this->container->get('clamd');

        $this->assertSame(true, $this->clamd->ping());
    }

    /**
     *
     */
    public function test_null()
    {
        $this->container->config()->set('clamd.enable', false);
        $this->clamd = $this->container->get('clamd');

        $this->assertInstanceOf(NullClamd::class, $this->clamd);
    }

    /**
     *
     */
    public function test_version()
    {
        $this->clamd = $this->container->get('clamd');

        $this->assertContains('ClamAV', $this->clamd->version());
    }

    /**
     *
     */
    public function test_multiple_calls()
    {
        $this->clamd = $this->container->get('clamd');

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
        $this->clamd = $this->container->get('clamd');

        $stats = $this->clamd->stats();

        $this->assertEquals(1, $stats['POOLS']);
    }

    /**
     *
     */
    public function test_scan_virus()
    {
        $this->clamd = $this->container->get('clamd');

        $result = $this->clamd->scanFile(__DIR__.'/_file/file_infected.pdf');

        $this->assertFalse($result);
        $this->assertSame('Clamav.Test.File-6', $this->clamd->getLastReason());
    }

    /**
     *
     */
    public function test_scan_ok()
    {
        $this->clamd = $this->container->get('clamd');

        $result = $this->clamd->scanFile(__DIR__.'/_file/image.jpg');

        $this->assertTrue($result);
        $this->assertNull($this->clamd->getLastReason());
    }

    /**
     *
     */
    public function test_legacy_scan()
    {
        $this->clamd = $this->container->get('clamd');

        $result = $this->clamd->fileScan(__DIR__.'/_file/image.jpg');

        $this->assertSame(Clamd::NO_VIRUS, $result['stats']);
    }
}
