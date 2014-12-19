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
    private $_subject;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $_mockMessageQueueGateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $_mockEventDispatcher;


    public function setUp()
    {
        $this->_mockMessageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->_mockEventDispatcher     = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->_subject = new StatusController($this->_mockMessageQueueGateway);
        $this->_subject->setEventDispatcher($this->_mockEventDispatcher);
    }

    public function testIndex()
    {
        $eventsByType     = ['events'];
        $messageQueueJobs = 10;

        $this->_mockMessageQueueGateway
        ->expects($this->once())
        ->method('getEventsByType')
        ->will($this->returnValue($eventsByType));

        $this->_mockMessageQueueGateway
        ->expects($this->once())
        ->method('countJobs')
        ->will($this->returnValue($messageQueueJobs));

        $actual_result = $this->_subject->index();

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

        $this->_mockMessageQueueGateway
        ->expects($this->once())
        ->method('deleteEvent')
        ->will($this->returnValue($job_id));


        $actual_result = $this->_subject->deleteJob($request);

        $this->assertTrue($actual_result);
    }

    public function testStartSelfUpdate()
    {
        $event = new SelfUpdateEvent(SelfUpdateEvent::TRIGGER);

        $this->_mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchInBackground')
        ->with($event);

        $actual_result = $this->_subject->startSelfUpdate();

        $this->assertTrue($actual_result);
    }
}
