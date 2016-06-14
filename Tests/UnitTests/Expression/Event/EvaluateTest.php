<?php

namespace Tests\Homie\Expression\Functions;

use Homie\Expression\Event\EvaluateEvent;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Expression\Event\Evaluate
 */
class EvaluateTest extends TestCase
{

    public function testEvent()
    {
        $event = new EvaluateEvent('test()');

        $this->assertEquals(EvaluateEvent::EVALUATE, $event->getEventName());
        $this->assertEquals('test()', $event->getExpression());
    }
}
