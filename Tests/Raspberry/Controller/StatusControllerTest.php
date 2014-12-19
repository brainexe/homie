<?php

namespace Tests\Raspberry\Controller\StatusController;

use BrainExe\Core\Application\SelfUpdate\SelfUpdateEvent;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Controller\StatusController;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\EventDispatcher\EventDispatcher;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers Raspberry\Controller\StatusController
 */
class StatusControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var StatusController
     */
    private $subject;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $mockMessageQueueGateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;


    public function setUp()
    {
        $this->mockMessageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->mockEventDispatcher     = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new StatusController($this->mockMessageQueueGateway);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testIndex()
    {
        $eventsByType     = ['events'];
        $messageQueueJobs = 10;

        $this->mockMessageQueueGateway
        ->expects($this->once())
        ->method('getEventsByType')
        ->will($this->returnValue($eventsByType));

        $this->mockMessageQueueGateway
        ->expects($this->once())
        ->method('countJobs')
        ->will($this->returnValue($messageQueueJobs));

        $actual_result = $this->subject->index();

        $expected_result = [
        'jobs' => $eventsByType,
        'stats' => [
        'Queue Len' => $messageQueueJobs
        ],
        ];

        $this->assertEquals($expected_result, $actual_result);
    }

    public function testDeleteJob()
    {
        $job_id = 10;
        $request = new Request();
        $request->request->set('job_id', $job_id);

        $this->mockMessageQueueGateway
        ->expects($this->once())
        ->method('deleteEvent')
        ->will($this->returnValue($job_id));


        $actual_result = $this->subject->deleteJob($request);

        $this->assertTrue($actual_result);
    }

    public function testStartSelfUpdate()
    {
        $event = new SelfUpdateEvent(SelfUpdateEvent::TRIGGER);

        $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchInBackground')
        ->with($event);

        $actual_result = $this->subject->startSelfUpdate();

        $this->assertTrue($actual_result);
    }
}
