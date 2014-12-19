<?php

namespace Raspberry\Tests\Radio;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\Util\TimeParser;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\RadioJob;
use Raspberry\Radio\VO\RadioVO;

class RadioJobTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RadioJob
     */
    private $subject;

    /**
     * @var TimeParser|MockObject
     */
    private $mock_time_parser;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $mock_message_queue_gateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mock_dispatcher;

    public function setUp()
    {
        $this->mock_time_parser = $this->getMock(TimeParser::class);
        $this->mock_message_queue_gateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->mock_dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new RadioJob($this->mock_message_queue_gateway, $this->mock_time_parser);
        $this->subject->setEventDispatcher($this->mock_dispatcher);
    }

    public function testGetJobs()
    {
        $jobs = [];

        $this->mock_message_queue_gateway
        ->expects($this->once())
        ->method('getEventsByType')
        ->with(RadioChangeEvent::CHANGE_RADIO)
        ->will($this->returnValue($jobs));

        $actual_result = $this->subject->getJobs();

        $this->assertEquals($jobs, $actual_result);
    }

    public function testAddJob()
    {
        $time_string = '1h';
        $timestamp = 1345465;
        $status = true;

        $radio_vo = new RadioVO();
        $radio_vo->id = 1;

        $this->mock_time_parser
        ->expects($this->once())
        ->method('parseString')
        ->with($time_string)
        ->will($this->returnValue($timestamp));

        $event = new RadioChangeEvent($radio_vo, $status);
        $this->mock_dispatcher
        ->expects($this->once())
        ->method('dispatchInBackground')
        ->with($event, $timestamp);

        $this->subject->addRadioJob($radio_vo, $time_string, $status);
    }

    public function testDeleteJob()
    {
        $job_id = 19;

        $this->mock_message_queue_gateway
        ->expects($this->once())
        ->method('deleteEvent')
        ->with($job_id, RadioChangeEvent::CHANGE_RADIO);

        $this->subject->deleteJob($job_id);

    }
}
