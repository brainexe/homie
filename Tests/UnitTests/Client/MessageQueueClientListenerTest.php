<?php

namespace Tests\Homie\Client;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Client\ExecuteCommandEvent;
use Homie\Client\Adapter\MessageQueueClient;
use Homie\Client\MessageQueueClientListener;
use Homie\Client\Adapter\LocalClient;

class MessageQueueClientListenerTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var MessageQueueClientListener
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->client = $this->getMock(LocalClient::class, [], [], '', false);
        $this->redis  = $this->getRedisMock();

        $this->subject = new MessageQueueClientListener($this->client);
        $this->subject->setRedis($this->redis);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleExecuteEventWithoutReturn()
    {
        $command = 'command';

        $event = new ExecuteCommandEvent($command, [], true);

        $output = 'output';

        $this->redis
            ->expects($this->once())
            ->method('lpush')
            ->with(MessageQueueClient::RETURN_CHANNEL, $output);

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($command, [])
            ->willReturn($output);

        $this->subject->handleExecuteEvent($event);
    }
}
