<?php

namespace Tests\Homie\EggTimer;

use BrainExe\Core\MessageQueue\Job;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\EggTimer\Controller;
use Homie\EggTimer\EggTimer;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers Homie\EggTimer\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var EggTimer|MockObject
     */
    private $timer;

    public function setUp()
    {
        $this->timer   = $this->createMock(EggTimer::class);
        $this->subject = new Controller($this->timer);
    }

    public function testAdd()
    {
        $time = 'time';
        $text = 'text';

        $request = new Request();
        $request->request->set('text', $text);
        $request->request->set('time', $time);

        $job = $this->createMock(Job::class);

        $this->timer
            ->expects($this->once())
            ->method('addNewJob')
            ->with($time, $text)
            ->willReturn($job);

        $actual = $this->subject->add($request);

        $this->assertEquals($job, $actual);
    }
}
