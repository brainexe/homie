<?php

namespace Tests\Raspberry\Client\MessageQueueClientListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Client\ExecuteCommandEvent;
use Raspberry\Client\MessageQueueClient;
use Raspberry\Client\MessageQueueClientListener;
use Raspberry\Client\LocalClient;
use BrainExe\Core\Redis\Redis;

class MessageQueueClientListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var MessageQueueClientListener
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $mockLocalClient;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);

        $this->subject = new MessageQueueClientListener($this->mockLocalClient);
        $this->subject->setRedis($this->mockRedis);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleExecuteEventWithoutReturn()
    {
        $command = 'command';

        $event = new ExecuteCommandEvent($command, true);

        $output = 'output';

        $this->mockRedis
            ->expects($this->once())
            ->method('lPush')
            ->with(MessageQueueClient::RETURN_CHANNEL, $output);

        $this->mockLocalClient
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($command)
            ->willReturn($output);

        $this->subject->handleExecuteEvent($event);
    }
}
