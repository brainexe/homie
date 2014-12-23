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
        ->willReturn($eventsByType);

        $this->mockMessageQueueGateway
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

        $this->mockMessageQueueGateway
        ->expects($this->once())
        ->method('deleteEvent')
        ->willReturn($jobId);


        $actualResult = $this->subject->deleteJob($request);

        $this->assertTrue($actualResult);
    }

    public function testStartSelfUpdate()
    {
        $event = new SelfUpdateEvent(SelfUpdateEvent::TRIGGER);

        $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchInBackground')
        ->with($event);

        $actualResult = $this->subject->startSelfUpdate();

        $this->assertTrue($actualResult);
    }
}
