<?php

namespace Tests\Homie\Node;

use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use Homie\Node;
use Homie\Node\Controller;
use Homie\Node\Gateway;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpFoundation\Request;

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
     * @var Gateway|MockObject
     */
    private $gateway;

    /**
     * @var int
     */
    private $nodeId = 42;

    public function setUp()
    {
        $this->idGenerator = $this->createMock(IdGenerator::class);
        $this->gateway     = $this->createMock(Gateway::class);

        $this->subject = new Controller($this->gateway, $this->nodeId);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testIndex()
    {
        $nodes = ['nodes'];

        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($nodes);

        $actual = $this->subject->index();
        $expected = [
            'nodes'     => $nodes,
            'currentId' => $this->nodeId,
            'types'     => Node::TYPES
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testDelete()
    {
        $request = new Request();
        $nodeId = 42;

        $this->gateway
            ->expects($this->once())
            ->method('delete')
            ->with($nodeId)
            ->willReturn(true);

        $actual = $this->subject->delete($request, $nodeId);

        $this->assertTrue($actual);
    }

    public function testAdd()
    {
        $request = new Request();
        $request->request->set('type', $type = 'mockType');
        $request->request->set('name', $name = 'mockName');
        $request->request->set('options', $options = []);
        $nodeId = 42;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($nodeId);

        $node = new Node($nodeId, $type, $name, $options);

        $this->gateway
            ->expects($this->once())
            ->method('save')
            ->with($node);

        $actual = $this->subject->add($request);

        $this->assertEquals($node, $actual);
    }

    public function testEdit()
    {
        $request = new Request();
        $request->request->set('name', $name = 'mockName');
        $request->request->set('options', $options = ['options']);
        $nodeId = 42;

        $node = new Node($nodeId, 'type', $name, $options);
        $node->setOptions($options);
        $node->setName($name);

        $this->gateway
            ->expects($this->once())
            ->method('get')
            ->with($nodeId)
            ->willReturn($node);

        $this->gateway
            ->expects($this->once())
            ->method('save')
            ->with($node);

        $actual = $this->subject->edit($request, $nodeId);

        $this->assertEquals($node, $actual);
    }
}
