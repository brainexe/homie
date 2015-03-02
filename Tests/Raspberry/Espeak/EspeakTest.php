<?php

namespace Tests\Raspberry\Espeak;

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
    private $messageQueueGateway;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    /**
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        parent::setUp();

        $this->messageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->client              = $this->getMock(LocalClient::class, [], [], '', false);
        $this->time                = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new Espeak($this->messageQueueGateway, $this->client);
        $this->subject->setTime($this->time);
    }

    public function testGetSpeakers()
    {
        $actualResult = $this->subject->getSpeakers();
        $this->assertInternalType('array', $actualResult);
    }

    public function testGetPendingJobs()
    {
        $now = 1000;
        $pendingJobs = [];

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->messageQueueGateway
            ->expects($this->once())
            ->method('getEventsByType')
            ->with(EspeakEvent::SPEAK, $now)
            ->willReturn($pendingJobs);

        $actualResult = $this->subject->getPendingJobs();

        $this->assertEquals($pendingJobs, $actualResult);
    }

    public function testSpeakWithEmptyText()
    {
        $text    = '';
        $volume  = 100;
        $speed   = 105;
        $speaker = 'de';

        $this->client
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

        $this->client
            ->expects($this->once())
            ->method('execute');

        $this->subject->speak($text, $volume, $speed, $speaker);
    }

    public function testDeleteJob()
    {
        $jobId = 12;

        $this->messageQueueGateway
            ->expects($this->once())
            ->method('deleteEvent')
            ->with($jobId, EspeakEvent::SPEAK);

        $this->subject->deleteJob($jobId);
    }
}
