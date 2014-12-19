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
    private $subject;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $mockMessageQueueGateway;

    /**
     * @var LocalClient|MockObject
     */
    private $mockLocalClient;

    /**
     * @var Time|MockObject
     */
    private $mockTime;

    public function setUp()
    {
        parent::setUp();

        $this->mockMessageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);
        $this->mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new Espeak($this->mockMessageQueueGateway, $this->mockLocalClient);
        $this->subject->setTime($this->mockTime);
    }

    public function testGetSpeakers()
    {
        $actual_result = $this->subject->getSpeakers();
        $this->assertInternalType('array', $actual_result);
    }

    public function testGetPendingJobs()
    {
        $now = 1000;
        $pending_jobs = [];

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->will($this->returnValue($now));

        $this->mockMessageQueueGateway
        ->expects($this->once())
        ->method('getEventsByType')
        ->with(EspeakEvent::SPEAK, $now)
        ->will($this->returnValue($pending_jobs));

        $actual_result = $this->subject->getPendingJobs();

        $this->assertEquals($pending_jobs, $actual_result);
    }

    public function testSpeakWithEmptyText()
    {
        $text = '';
        $volume = 100;
        $speed = 105;
        $speaker = 'de';

        $this->mockLocalClient
        ->expects($this->never())
        ->method('execute');

        $this->subject->speak($text, $volume, $speed, $speaker);
    }
    public function testSpeak()
    {
        $text = 'text';
        $volume = 100;
        $speed = 105;
        $speaker = 'de';

        $this->mockLocalClient
        ->expects($this->once())
        ->method('execute');

        $this->subject->speak($text, $volume, $speed, $speaker);
    }

    public function testDeleteJob()
    {
        $job_id = 12;

        $this->mockMessageQueueGateway
        ->expects($this->once())
        ->method('deleteEvent')
        ->with($job_id, EspeakEvent::SPEAK);

        $this->subject->deleteJob($job_id);
    }
}
