<?php

namespace Tests\Homie\Display;

use ArrayIterator;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Display\Controller;
use Homie\Display\Gateway;
use Homie\Display\Renderer;
use Homie\Display\Settings;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Renderer|MockObject
     */
    private $renderer;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->gateway    = $this->createMock(Gateway::class);
        $this->renderer   = $this->createMock(Renderer::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->subject    = new Controller($this->gateway, $this->renderer);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testAddDisplay()
    {
        $request = new Request();
        $request->request->set('lines', $lines = 4);
        $request->request->set('columns', $columns = 5);
        $request->request->set('content', $content = ['content']);

        $rendered = ['rendered'];

        $settings = new Settings();
        $settings->lines    = $lines;
        $settings->columns  = $columns;
        $settings->content  = $content;
        $settings->rendered = $rendered;

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->willReturn($rendered);

        $this->gateway
            ->expects($this->once())
            ->method('addDisplay')
            ->with($settings);

        $actual = $this->subject->add($request);

        $this->assertEquals($settings, $actual);
    }

    public function testEditDisplay()
    {
        $displayId = 42;

        $request = new Request();
        $request->request->set('lines', $lines = 4);
        $request->request->set('columns', $columns = 5);
        $request->request->set('content', $content = ['content']);

        $rendered = ['rendered'];

        $settings = new Settings();
        $settings->displayId = $displayId;
        $settings->lines     = $lines;
        $settings->columns   = $columns;
        $settings->content   = $content;
        $settings->rendered  = $rendered;

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->willReturn($rendered);

        $this->gateway
            ->expects($this->once())
            ->method('update')
            ->with($settings);

        $actual = $this->subject->update($request, $displayId);

        $this->assertEquals($settings, $actual);
    }

    public function testRedraw()
    {

        $displayId = 11880;
        $request = new Request();

        $settings = new Settings();
        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($settings)
            ->willReturn(['foo', 'bar']);

        $this->gateway
            ->expects($this->once())
            ->method('get')
            ->with($displayId)
            ->willReturn($settings);

        $actual = $this->subject->redraw($request, $displayId);

        $this->assertEquals($settings, $actual);
        $this->assertEquals(['foo', 'bar'], $actual->content);
    }

    public function testIndex()
    {
        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn(new ArrayIterator(['data']));

        $actual = $this->subject->index();

        $expected = [
            'screens' => ['data']
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testDelete()
    {
        $displayId = 11880;

        $request = new Request();

        $this->gateway
            ->expects($this->once())
            ->method('delete')
            ->with($displayId);

        $actual = $this->subject->delete($request, $displayId);

        $this->assertTrue($actual);
    }
}
