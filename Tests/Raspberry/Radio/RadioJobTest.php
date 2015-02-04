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
    private $mockTimeParser;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $mockMessageQueueGateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockDispatcher;

    public function setUp()
    {
        $this->mockTimeParser = $this->getMock(TimeParser::class);
        $this->mockMessageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->mockDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new RadioJob($this->mockMessageQueueGateway, $this->mockTimeParser);
        $this->subject->setEventDispatcher($this->mockDispatcher);
    }

    public function testGetJobs()
    {
        $jobs = [];

        $this->mockMessageQueueGateway
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

        $this->mockTimeParser
            ->expects($this->once())
            ->method('parseString')
            ->with($timeString)
            ->willReturn($timestamp);

        $event = new RadioChangeEvent($radioVo, $status);
        $this->mockDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, $timestamp);

        $this->subject->addRadioJob($radioVo, $timeString, $status);
    }

    public function testDeleteJob()
    {
        $jobId = 19;

        $this->mockMessageQueueGateway
            ->expects($this->once())
            ->method('deleteEvent')
            ->with($jobId, RadioChangeEvent::CHANGE_RADIO);

        $this->subject->deleteJob($jobId);

    }
}
