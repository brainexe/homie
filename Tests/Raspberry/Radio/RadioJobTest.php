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
    private $timeParser;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $gateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->timeParser = $this->getMock(TimeParser::class);
        $this->gateway    = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new RadioJob($this->gateway, $this->timeParser);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testGetJobs()
    {
        $jobs = [];

        $this->gateway
            ->expects($this->once())
            ->method('getEventsByType')
            ->with(RadioChangeEvent::CHANGE_RADIO)
            ->willReturn($jobs);

        $actualResult = $this->subject->getJobs();

        $this->assertEquals($jobs, $actualResult);
    }

    public function testAddJob()
    {
        $timeString = '1h';
        $timestamp  = 1345465;
        $status     = true;

        $radioVo = new RadioVO();
        $radioVo->radioId = 1;

        $this->timeParser
            ->expects($this->once())
            ->method('parseString')
            ->with($timeString)
            ->willReturn($timestamp);

        $event = new RadioChangeEvent($radioVo, $status);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, $timestamp);

        $this->subject->addRadioJob($radioVo, $timeString, $status);
    }

    public function testDeleteJob()
    {
        $jobId = 19;

        $this->gateway
            ->expects($this->once())
            ->method('deleteEvent')
            ->with($jobId, RadioChangeEvent::CHANGE_RADIO);

        $this->subject->deleteJob($jobId);

    }
}
