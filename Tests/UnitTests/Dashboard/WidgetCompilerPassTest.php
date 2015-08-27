<?php

namespace Tests\Homie\Dashboard\WidgetCompilerPass;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Dashboard\WidgetCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers Homie\Dashboard\WidgetCompilerPass
 */
class WidgetCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var WidgetCompilerPass
     */
    private $subject;

    /**
     * @var MockObject|ContainerBuilder
     */
    private $mockContainer;

    public function setUp()
    {
        $this->subject = new WidgetCompilerPass();
        $this->mockContainer = $this->getMock(ContainerBuilder::class);
    }

    public function testProcess()
    {
        $widget_factory    = $this->getMock(Definition::class);
        $widget_definition = $this->getMock(Definition::class);
        $widget_id         = 'widget_1';

        $this->mockContainer
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('WidgetFactory')
            ->willReturn($widget_factory);

        $this->mockContainer
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(WidgetCompilerPass::TAG)
            ->will($this->returnValue([
        $widget_id => $widget_definition
        ]));

        $widget_factory
            ->expects($this->once())
            ->method('addMethodCall')
            ->with('addWidget', [new Reference($widget_id)]);

        $this->subject->process($this->mockContainer);
    }
}
