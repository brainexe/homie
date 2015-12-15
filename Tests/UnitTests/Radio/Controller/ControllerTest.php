<?php

namespace Tests\Homie\Radio\Controller;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Radio\Controller\Controller;
use Homie\Radio\SwitchChangeEvent;
use Homie\Radio\VO\RadioVO;
use Symfony\Component\HttpFoundation\Request;
use Homie\Radio\Radios;
use Homie\Radio\Job;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Homie\Radio\Controller\Controller
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
     * @var Job|MockObject
     */
    private $job;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->radio      = $this->getMock(Radios::class, [], [], '', false);
        $this->job        = $this->getMock(Job::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Controller($this->radio, $this->job);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testIndex()
    {
        $radiosFormatted = ['radios_formatted'];

        $this->radio
            ->expects($this->once())
            ->method('getRadios')
            ->willReturn(new ArrayIterator($radiosFormatted));

        $actual = $this->subject->index();

        $expected = [
             'radios' => $radiosFormatted,
             'pins'   => Radios::PINS,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSetStatus()
    {
        $request  = new Request();
        $switchId = 10;
        $status   = true;
        $radioVo  = new RadioVO();
        $event    = new SwitchChangeEvent($radioVo, $status);

        $this->radio
            ->expects($this->once())
            ->method('get')
            ->with($switchId)
            ->willReturn($radioVo);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actual = $this->subject->setStatus($request, $switchId, $status);

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
        $switchId = 10;

        $this->radio
            ->expects($this->once())
            ->method('delete')
            ->with($switchId);

        $actual = $this->subject->deleteRadio($request, $switchId);

        $this->assertTrue($actual);
    }

    public function testAddJob()
    {
        $switchId    = 10;
        $status     = false;
        $timeString = 'time';

        $switch = new RadioVO();

        $request = new Request();
        $request->request->set('radioId', $switchId);
        $request->request->set('status', $status);
        $request->request->set('time', $timeString);

        $this->radio
            ->expects($this->once())
            ->method('get')
            ->with($switchId)
            ->willReturn($switch);

        $this->job
            ->expects($this->once())
            ->method('addJob')
            ->with($switch, $timeString, $status);

        $actual = $this->subject->addJob($request);

        $this->assertTrue($actual);
    }
}
