<?php

namespace Tests\Homie\Expression\Controller;

use BrainExe\Core\EventDispatcher\CronEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use Homie\Expression\Controller\Cron;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

class CronTest extends TestCase
{

    /**
     * @var Cron
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setup()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Cron(
            $this->dispatcher
        );
    }

    public function testAdd()
    {
        $event = new CronEvent(
            new TimingEvent('daily'),
            '@daily'
        );
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $request = new Request();
        $request->request->set('expression', '@daily');
        $request->request->set('cronId', 'daily');
        $actual = $this->subject->addCron($request);

        $this->assertTrue($actual);
    }
}
