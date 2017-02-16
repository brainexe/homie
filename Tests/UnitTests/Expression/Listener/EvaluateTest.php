<?php

namespace Tests\Homie\Expression\Listener;

use Homie\Expression\Event\EvaluateEvent;
use Homie\Expression\Language;
use Homie\Expression\Listener\Evaluate;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

class EvaluateTest extends TestCase
{

    /**
     * @var Evaluate
     */
    private $subject;

    /**
     * @var Language|MockObject
     */
    private $language;

    public function setup()
    {
        $this->language = $this->createMock(Language::class);

        $this->subject = new Evaluate(
            $this->language
        );
    }

    public function testEvaluate()
    {
        $event = new EvaluateEvent('myExpression', ['foo']);

        $this->language
            ->expects($this->once())
            ->method('evaluate')
            ->with('myExpression', ['foo']);

        $this->subject->evaluate($event);
    }
}
