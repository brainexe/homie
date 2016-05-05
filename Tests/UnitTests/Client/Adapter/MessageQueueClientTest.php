<?php
namespace Tests\Homie\Adapter\Client;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Client\ExecuteCommandEvent;
use Homie\Client\Adapter\MessageQueueClient;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Homie\Client\Adapter\MessageQueueClient
 */
class MessageQueueClientTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var MessageQueueClient
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject = new MessageQueueClient();
        $this->subject->setRedis($this->redis);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testExecute()
    {
        $command = 'command';

        $event = new ExecuteCommandEvent($command, [], false);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, 0);

        $this->subject->execute($command);
    }

    public function testExecuteWithReturn()
    {
        $command = 'command';

        $event = new ExecuteCommandEvent($command, [], true);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->redis
            ->expects($this->once())
            ->method('brpop')
            ->with(MessageQueueClient::RETURN_CHANNEL, MessageQueueClient::TIMEOUT)
            ->willReturn('result');

        $actual = $this->subject->executeWithReturn($command);

        $this->assertEquals('result', $actual);
    }
}
