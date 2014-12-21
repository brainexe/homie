<?php

namespace Tests\Raspberry\Controller\EspeakController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

use Raspberry\Controller\EspeakController;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;

use Symfony\Component\HttpFoundation\Request;
use Raspberry\Espeak\Espeak;
use BrainExe\Core\Util\TimeParser;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Controller\EspeakController
 */
class EspeakControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var EspeakController
     */
    private $subject;

    /**
     * @var Espeak|MockObject
     */
    private $mockEspeak;

    /**
     * @var TimeParser|MockObject
     */
    private $mockTimeParser;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockEspeak = $this->getMock(Espeak::class, [], [], '', false);
        $this->mockTimeParser = $this->getMock(TimeParser::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new EspeakController($this->mockEspeak, $this->mockTimeParser);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testIndex()
    {
        $speakers = ['speakers'];
        $jobs = ['jobs'];

        $this->mockEspeak
        ->expects($this->once())
        ->method('getSpeakers')
        ->will($this->returnValue($speakers));

        $this->mockEspeak
        ->expects($this->once())
        ->method('getPendingJobs')
        ->will($this->returnValue($jobs));

        $actualResult = $this->subject->index();

        $expectedResult = [
        'speakers' => $speakers,
        'jobs' => $jobs
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSpeak()
    {
        $request = new Request();

        $speaker = 'speaker';
        $text = 'text';
        $volume = 120;
        $speed = 80;
        $delay_raw = 'delay_row';
        $timestamp = 10;

        $request->request->set('speaker', $speaker);
        $request->request->set('text', $text);
        $request->request->set('volume', $volume);
        $request->request->set('speed', $speed);
        $request->request->set('delay', $delay_raw);

        $this->mockTimeParser
        ->expects($this->once())
        ->method('parseString')
        ->with($delay_raw)
        ->will($this->returnValue($timestamp));

        $espeak_vo = new EspeakVO($text, $volume, $speed, $speaker);
        $event = new EspeakEvent($espeak_vo);

        $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchInBackground')
        ->with($event, $timestamp);

        $pending_jobs = ['pending_jobs'];

        $this->mockEspeak
        ->expects($this->once())
        ->method('getPendingJobs')
        ->will($this->returnValue($pending_jobs));

        $actualResult = $this->subject->speak($request);

        $this->assertEquals($pending_jobs, $actualResult);
    }

    public function testDeleteJobJob()
    {
        $jobId = 10;
        $request = new Request();
        $request->request->set('job_id', $jobId);

        $this->mockEspeak
        ->expects($this->once())
        ->method('deleteJob')
        ->will($this->returnValue($jobId));


        $actualResult = $this->subject->deleteJob($request);

        $this->assertTrue($actualResult);
    }
}
