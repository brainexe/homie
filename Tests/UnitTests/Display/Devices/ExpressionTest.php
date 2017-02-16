<?php

namespace Tests\Homie\Display\Devices;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Display\Devices\Expression;
use Homie\Expression\Event\EvaluateEvent;
use Homie\Node;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Display\Devices\Expression
 */
class ExpressionTest extends TestCase
{

    /**
     * @var Expression
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Expression($this->dispatcher);
    }

    public function testOutput()
    {
        $string = "foo";

        $node = new Node(1010, Node::TYPE_SERVER);
        $node->setOptions(['displayFunction' => 'drawDisplay(content)']);

        $event = new EvaluateEvent('drawDisplay(content)', [
            'content' => $string
        ]);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->subject->display($node, $string);
    }
}
