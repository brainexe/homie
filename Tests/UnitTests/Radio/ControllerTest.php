<?php

namespace Tests\Homie\Radio;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Radio\Controller;
use Homie\Radio\RadioChangeEvent;
use Homie\Radio\VO\RadioVO;
use Symfony\Component\HttpFoundation\Request;
use Homie\Radio\Radios;
use Homie\Radio\RadioJob;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Homie\Radio\Controller
 */
class ControllerTest extends TestCase
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
            ->willReturn(new ArrayIterator($radiosFormatted));

        $actual = $this->subject->index();

        $expected = [
             'radios'    => $radiosFormatted,
             'pins'      => Radios::$radioPins,
        ];

        $this->assertEquals($expected, $actual);
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

        $actual = $this->subject->setStatus($request, $radioId, $status);

        $this->assertEquals(true, $actual);
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

        $actual = $this->subject->addRadio($request);

        $this->assertEquals($radioVo, $actual);
    }

    public function testDeleteRadio()
    {
        $request = new Request();
        $radioId = 10;

        $this->radio
            ->expects($this->once())
            ->method('deleteRadio')
            ->with($radioId);

        $actual = $this->subject->deleteRadio($request, $radioId);

        $this->assertTrue($actual);
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

        $actual = $this->subject->addRadioJob($request);

        $this->assertTrue($actual);
    }
}
