<?php

namespace Tests\Raspberry\Notification\MessageQueueNotifications;

use BrainExe\Core\Redis\Redis;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Notification\MessageQueueNotifications;
use BrainExe\MessageQueue\MessageQueueGateway;

/**
 * @Covers Raspberry\Notification\MessageQueueNotifications
 */
class MessageQueueNotificationsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var MessageQueueNotifications
     */
    private $subject;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $mockMessageQueueGateway;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockMessageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);

        $this->subject = new MessageQueueNotifications();
        $this->subject->setMessageQueueGateway($this->mockMessageQueueGateway);
        $this->subject->setRedis($this->mockRedis);
    }

    public function testGetNotification()
    {
        $count = 10;

        $this->mockMessageQueueGateway
        ->expects($this->once())
        ->method('countJobs')
        ->will($this->returnValue($count));

        $actualResult = $this->subject->getNotification();

        $this->assertEquals($count, $actualResult);
    }
}
