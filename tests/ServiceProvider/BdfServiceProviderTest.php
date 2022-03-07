<?php

namespace Test\Clamd;

use Bdf\Config\Config;
use Bdf\Web\Application;
use Clamd\Clamd;
use Clamd\NullClamd;
use Clamd\ServiceProvider\ClamdServiceProvider;
use PHPUnit\Framework\TestCase;

/**
 * @group bdf
 */
class BdfServiceProviderTest extends TestCase
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
        if (!class_exists(Application::class)) {
            $this->markTestSkipped('b2p/bdf-web not installed');
        }

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
}
