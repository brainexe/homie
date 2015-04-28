<?php

namespace Tests\Raspberry\Status;

use BrainExe\Core\Application\SelfUpdate\SelfUpdateEvent;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use BrainExe\MessageQueue\Gateway;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Raspberry\Status\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers Raspberry\Status\Controller
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->gateway    = $this->getMock(Gateway::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Controller($this->gateway);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testIndex()
    {
        $eventsByType     = ['events'];
        $messageQueueJobs = 10;

        $this->gateway
            ->expects($this->once())
            ->method('getEventsByType')
            ->willReturn($eventsByType);

        $this->gateway
            ->expects($this->once())
            ->method('countJobs')
            ->willReturn($messageQueueJobs);

        $actualResult = $this->subject->index();

        $expectedResult = [
            'jobs' => $eventsByType,
            'stats' => [
                'Queue Len' => $messageQueueJobs
            ],
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testDeleteJob()
    {
        $jobId = 10;
        $request = new Request();
        $request->request->set('job_id', $jobId);

        $this->gateway
            ->expects($this->once())
            ->method('deleteEvent')
            ->willReturn($jobId);


        $actualResult = $this->subject->deleteJob($request);

        $this->assertTrue($actualResult);
    }

    public function testStartSelfUpdate()
    {
        $event = new SelfUpdateEvent(SelfUpdateEvent::TRIGGER);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actualResult = $this->subject->startSelfUpdate();

        $this->assertTrue($actualResult);
    }
}
