<?php

namespace Tests\Homie\Adapter\Client;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Client\Adapter\DummyClient;
use Monolog\Logger;

/**
 * @covers \Homie\Client\Adapter\DummyClient
 */
class DummyClientTest extends TestCase
{

    /**
     * @var DummyClient
     */
    private $subject;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(Logger::class);

        $this->subject = new DummyClient();
        $this->subject->setLogger($this->logger);
    }

    public function testExecute()
    {
        $command = 'test';

        $this->logger
            ->expects($this->once())
            ->method('log')
            ->with('info', 'test ');

        $this->subject->execute($command);
    }

    public function testExecuteWithReturn()
    {
        $command = 'test';

        $this->logger
            ->expects($this->once())
            ->method('log')
            ->with('info', 'test ');

        $actualResult = $this->subject->executeWithReturn($command);

        $this->assertEquals('', $actualResult);
    }
}
