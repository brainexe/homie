<?php

namespace Tests\Homie\Expression\Functions;

use Homie\Expression\Event\EvaluateEvent;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Expression\Event\EvaluateEvent
 */
class EvaluateEventTest extends TestCase
{

    public function testEvent()
    {
        $event = new EvaluateEvent('test()', ['foo']);

        $this->assertEquals(EvaluateEvent::EVALUATE, $event->getEventName());
        $this->assertEquals('test()', $event->getExpression());
        $this->assertEquals(['foo'], $event->getVariables());
    }
}
