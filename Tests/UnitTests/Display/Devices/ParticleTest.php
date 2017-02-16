<?php

namespace Tests\Homie\Display\Devices;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Expression\Event\EvaluateEvent;
use Homie\Display\Devices\Particle;
use Homie\Node;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Display\Devices\Particle
 */
class ParticleTest extends TestCase
{

    /**
     * @var Particle
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Particle($this->dispatcher);
    }

    public function testOutput()
    {
        $string = "foo";

        $node = new Node(1010, Node::TYPE_SERVER);
        $node->setOptions(['displayFunction' => 'drawDisplay']);

        $event = new EvaluateEvent('callParticleFunction(1010, "drawDisplay", "foo")');

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->subject->display($node, $string);
    }
}
