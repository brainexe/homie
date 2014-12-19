<?php

namespace Tests\Raspberry\Client\MessageQueueClient;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Client\ExecuteCommandEvent;
use Raspberry\Client\MessageQueueClient;
use BrainExe\Core\Redis\Redis;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Client\MessageQueueClient
 */
class MessageQueueClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var MessageQueueClient
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject = new MessageQueueClient();
        $this->subject->setRedis($this->mockRedis);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testExecute()
    {
        $command = 'command';

        $event = new ExecuteCommandEvent($command, false);

        $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchInBackground')
        ->with($event, 0);

        $this->subject->execute($command);
    }

    public function testExecuteWithReturn()
    {
        $command = 'command';

        $event = new ExecuteCommandEvent($command, true);

        $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchInBackground')
        ->with($event);

        $this->mockRedis
        ->expects($this->once())
        ->method('brPop')
        ->with(MessageQueueClient::RETURN_CHANNEL, 5);

        $this->subject->executeWithReturn($command);
    }
}
