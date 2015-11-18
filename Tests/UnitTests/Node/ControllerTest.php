<?php

namespace Tests\Homie\Node;

use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use Homie\Node;
use Homie\Node\Controller;
use Homie\Node\Gateway;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @covers Homie\Node\Controller
 */
class ControllerTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    /**
     * @var Node|MockObject
     */
    private $node;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    public function setUp()
    {
        $this->idGenerator = $this->getMock(IdGenerator::class);
        $this->node        = $this->getMock(Node::class, [], [42]);
        $this->gateway     = $this->getMock(Gateway::class);

        $this->subject = new Controller($this->node, $this->gateway);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testIndex()
    {
        $currentId = 42;
        $nodes = ['nodes'];

        $this->node
            ->expects($this->once())
            ->method('getNodeId')
            ->willReturn($currentId);

        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($nodes);

        $actual = $this->subject->index();
        $expected = [
            'nodes'     => $nodes,
            'currentId' => $currentId
        ];

        $this->assertEquals($expected, $actual);
    }
}
