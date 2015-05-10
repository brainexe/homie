<?php

namespace Tests\Homie\Radio;

use BrainExe\Core\Controller\ControllerInterface;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Radio\Controller;
use Homie\Radio\RadioChangeEvent;
use Homie\Radio\VO\RadioVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Homie\Radio\Radios;
use Homie\Radio\RadioJob;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Homie\Radio\Controller
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Radios|MockObject
     */
    private $radio;

    /**
     * @var RadioJob|MockObject
     */
    private $radioJob;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->radio      = $this->getMock(Radios::class, [], [], '', false);
        $this->radioJob   = $this->getMock(RadioJob::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Controller($this->radio, $this->radioJob);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testIndex()
    {
        $radiosFormatted = ['radios_formatted'];
        $jobs = ['jobs'];

        $this->radio
            ->expects($this->once())
            ->method('getRadios')
            ->willReturn($radiosFormatted);

        $this->radioJob
            ->expects($this->once())
            ->method('getJobs')
            ->willReturn($jobs);

        $actualResult = $this->subject->index();

        $expectedResult = [
                'radios'    => $radiosFormatted,
                'radioJobs' => $jobs,
                'pins'      => Radios::$radioPins,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSetStatus()
    {
        $request  = new Request();
        $radioId  = 10;
        $status   = true;
        $radioVo  = new RadioVO();
        $event    = new RadioChangeEvent($radioVo, $status);

        $this->radio
            ->expects($this->once())
            ->method('getRadio')
            ->with($radioId)
            ->willReturn($radioVo);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actualResult = $this->subject->setStatus($request, $radioId, $status);

        $this->assertEquals(true, $actualResult);
    }

    public function testAddRadio()
    {
        $name        = 'name';
        $description = 'description';
        $code        = 12;
        $pinRaw      = 'A';
        $pin         = 1;

        $request = new Request();
        $request->request->set('name', $name);
        $request->request->set('description', $description);
        $request->request->set('code', $code);
        $request->request->set('pin', $pinRaw);

        $radioVo = new RadioVO();
        $radioVo->name        = $name;
        $radioVo->description = $description;
        $radioVo->code        = $code;
        $radioVo->pin         = $pin;

        $this->radio
            ->expects($this->once())
            ->method('addRadio')
            ->with($radioVo);

        $this->radio
            ->expects($this->once())
            ->method('getRadioPin')
            ->with($pinRaw)
            ->willReturn($pin);

        $actualResult = $this->subject->addRadio($request);

        $this->assertEquals($radioVo, $actualResult);
    }

    public function testDeleteRadio()
    {
        $request = new Request();
        $radioId = 10;

        $this->radio
            ->expects($this->once())
            ->method('deleteRadio')
            ->with($radioId);

        $actualResult = $this->subject->deleteRadio($request, $radioId);

        $this->assertTrue($actualResult);
    }

    public function testAddRadioJob()
    {
        $radioId    = 10;
        $status     = false;
        $timeString = 'time';

        $radioVo = new RadioVO();

        $request = new Request();
        $request->request->set('radioId', $radioId);
        $request->request->set('status', $status);
        $request->request->set('time', $timeString);

        $this->radio
            ->expects($this->once())
            ->method('getRadio')
            ->with($radioId)
            ->willReturn($radioVo);

        $this->radioJob
            ->expects($this->once())
            ->method('addRadioJob')
            ->with($radioVo, $timeString, $status);

        $this->radioJob
            ->expects($this->once())
            ->method('getJobs')
            ->willReturn([]);

        $actualResult = $this->subject->addRadioJob($request);

        $this->assertEquals([], $actualResult);
    }

    public function testDeleteRadioJob()
    {
        $request = new Request();
        $radioId = 10;

        $this->radioJob
            ->expects($this->once())
            ->method('deleteJob')
            ->with($radioId);

        $actualResult = $this->subject->deleteRadioJob($request, $radioId);

        $this->assertTrue($actualResult);
    }
}
