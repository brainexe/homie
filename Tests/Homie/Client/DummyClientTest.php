<?php

namespace Tests\Homie\Client\DummyClient;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Client\DummyClient;
use Monolog\Logger;

/**
 * @covers Homie\Client\DummyClient
 */
class DummyClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var DummyClient
     */
    private $subject;

    /**
     * @var Logger|MockObject
     */
    private $mockLogger;

    public function setUp()
    {
        $this->mockLogger = $this->getMock(Logger::class, [], [], '', false);

        $this->subject = new DummyClient();
        $this->subject->setLogger($this->mockLogger);
    }

    public function testExecute()
    {
        $command = 'test';

        $this->mockLogger
            ->expects($this->once())
            ->method('log')
            ->with('info', $command);

        $this->subject->execute($command);
    }

    public function testExecuteWithReturn()
    {
        $command = 'test';

        $this->mockLogger
            ->expects($this->once())
            ->method('log')
            ->with('info', $command);

        $actualResult = $this->subject->executeWithReturn($command);

        $this->assertEquals('', $actualResult);
    }
}
