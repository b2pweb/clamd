<?php

namespace Test\Clamd;

use Clamd\Clamd;
use PHPUnit\Framework\TestCase;
use Xenolope\Quahog\Client;
use Xenolope\Quahog\Exception\ConnectionException;

/**
 *
 */
class ClamdTest extends TestCase
{
    public function test_ping()
    {
        $client = $this->createMock(Client::class);
        $clamd = new Clamd(null);
        $clamd->setClient($client);

        $client->expects($this->once())->method('ping')->willReturn(true);

        $this->assertSame(true, $clamd->ping());
    }

    public function test_ping_error()
    {
        $client = $this->createMock(Client::class);
        $clamd = new Clamd(null);
        $clamd->setClient($client);

        $client->expects($this->once())->method('ping')->willThrowException(new ConnectionException('foo'));

        $this->assertSame(false, $clamd->ping());
    }
}