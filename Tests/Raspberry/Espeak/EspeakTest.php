<?php

namespace Tests\Raspberry\Espeak\Espeak;

use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Espeak\Espeak;
use BrainExe\MessageQueue\MessageQueueGateway;
use Raspberry\Client\LocalClient;
use Raspberry\Espeak\EspeakEvent;

/**
 * @Covers Raspberry\Espeak\Espeak
 */
class EspeakTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Espeak
     */
    private $_subject;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $_mockMessageQueueGateway;

    /**
     * @var LocalClient|MockObject
     */
    private $_mockLocalClient;

    /**
     * @var Time|MockObject
     */
    private $_mockTime;

    public function setUp()
    {
        parent::setUp();

        $this->_mockMessageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->_mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);
        $this->_mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->_subject = new Espeak($this->_mockMessageQueueGateway, $this->_mockLocalClient);
        $this->_subject->setTime($this->_mockTime);
    }

    public function testGetSpeakers()
    {
        $actual_result = $this->_subject->getSpeakers();
        $this->assertInternalType('array', $actual_result);
    }

    public function testGetPendingJobs()
    {
        $now = 1000;
        $pending_jobs = [];

        $this->_mockTime
        ->expects($this->once())
        ->method('now')
        ->will($this->returnValue($now));

        $this->_mockMessageQueueGateway
        ->expects($this->once())
        ->method('getEventsByType')
        ->with(EspeakEvent::SPEAK, $now)
        ->will($this->returnValue($pending_jobs));

        $actual_result = $this->_subject->getPendingJobs();

        $this->assertEquals($pending_jobs, $actual_result);
    }

    public function testSpeakWithEmptyText()
    {
        $text = '';
        $volume = 100;
        $speed = 105;
        $speaker = 'de';

        $this->_mockLocalClient
        ->expects($this->never())
        ->method('execute');

        $this->_subject->speak($text, $volume, $speed, $speaker);
    }
    public function testSpeak()
    {
        $text = 'text';
        $volume = 100;
        $speed = 105;
        $speaker = 'de';

        $this->_mockLocalClient
        ->expects($this->once())
        ->method('execute');

        $this->_subject->speak($text, $volume, $speed, $speaker);
    }

    public function testDeleteJob()
    {
        $job_id = 12;

        $this->_mockMessageQueueGateway
        ->expects($this->once())
        ->method('deleteEvent')
        ->with($job_id, EspeakEvent::SPEAK);

        $this->_subject->deleteJob($job_id);
    }
}
