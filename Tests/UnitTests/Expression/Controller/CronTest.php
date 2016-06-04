<?php

namespace Tests\Homie\Expression\Controller;

use BrainExe\Core\Cron\Expression;
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

    /**
     * @var Expression|MockObject
     */
    private $expression;

    public function setup()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->expression = $this->createMock(Expression::class);

        $this->subject = new Cron(
            $this->dispatcher,
            $this->expression
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

    public function testNext()
    {
        $this->expression
            ->expects($this->once())
            ->method('getNextRun')
            ->with('@daily')
            ->willReturn(123456);

        $request = new Request();
        $request->request->set('expression', '@daily');

        $actual = $this->subject->getNextTime($request);

        $this->assertEquals(123456, $actual);
    }
}
