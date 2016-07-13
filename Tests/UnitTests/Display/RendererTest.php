<?php

namespace Tests\Homie\Display;

use Homie\Display\Devices\DeviceInterface;
use Homie\Display\Devices\Factory;
use Homie\Expression\Language;
use Homie\Display\Renderer;
use Homie\Display\Settings;
use Homie\Node;
use Homie\Node\Gateway as NodeGateway;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class RendererTest extends TestCase
{

    /**
     * @var Renderer
     */
    private $subject;

    /**
     * @var Language|MockObject
     */
    private $language;

    /**
     * @var Factory|MockObject
     */
    private $factory;

    /**
     * @var NodeGateway|MockObject
     */
    private $nodes;

    public function setUp()
    {
        $this->language = $this->createMock(Language::class);
        $this->factory  = $this->createMock(Factory::class);
        $this->nodes    = $this->createMock(NodeGateway::class);

        $this->subject = new Renderer(
            $this->language,
            $this->factory,
            $this->nodes
        );
    }

    public function testRenderWithoutDisplay()
    {
        $settings = new Settings();
        $settings->content = [
            'line 1',
            'line 2',
        ];

        $this->language
            ->expects($this->exactly(2))
            ->method('evaluate')
            ->willReturnMap([
                ['line 1', [], 'result 1'],
                ['line 2', [], 'result 2']
            ]);

        $actual = $this->subject->render($settings);

        $this->assertEquals(['result 1', 'result 2'], $actual);
    }

    public function testRenderWithDisplay()
    {
        $settings = new Settings();
        $settings->content = [
            'line 1',
            'line 2',
        ];
        $settings->nodeId = 1212;

        /** @var DeviceInterface|MockObject $device */
        $device = $this->createMock(DeviceInterface::class);

        $node = new Node(1212, Node::TYPE_DISPLAY, '', [
            'deviceType' => 'myDevice'
        ]);

        $this->nodes
            ->expects($this->once())
            ->method('get')
            ->with(1212)
            ->willReturn($node);

        $this->factory
            ->expects($this->once())
            ->method('getDevice')
            ->with('myDevice')
            ->willReturn($device);

        $device
            ->expects($this->once())
            ->method('display')
            ->with($node, "result 1\nresult 2");

        $this->language
            ->expects($this->exactly(2))
            ->method('evaluate')
            ->willReturnMap([
                ['line 1', [], 'result 1'],
                ['line 2', [], 'result 2']
            ]);

        $actual = $this->subject->render($settings);

        $this->assertEquals(['result 1', 'result 2'], $actual);
    }
}
