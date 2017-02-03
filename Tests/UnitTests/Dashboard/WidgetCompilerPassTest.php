<?php

namespace Tests\Homie\Dashboard;

use Homie\Dashboard\WidgetFactory;
use Homie\Dashboard\Widgets\Time;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Dashboard\WidgetCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers Homie\Dashboard\WidgetCompilerPass
 */
class WidgetCompilerPassTest extends TestCase
{

    /**
     * @var WidgetCompilerPass
     */
    private $subject;

    /**
     * @var MockObject|ContainerBuilder
     */
    private $container;

    public function setUp()
    {
        $this->subject   = new WidgetCompilerPass();
        $this->container = $this->createMock(ContainerBuilder::class);
    }

    public function testProcess()
    {
        $widgetFactory    = $this->createMock(Definition::class);
        $widgetDefinition = $this->createMock(Definition::class);
        $widgetId         = 'widget_1';

        $this->container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with(WidgetFactory::class)
            ->willReturn($widgetFactory);

        $this->container
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(WidgetCompilerPass::TAG)
            ->will($this->returnValue([
                $widgetId => null
            ]));

        $this->container
            ->expects($this->at(2))
            ->method('getDefinition')
            ->with($widgetId)
            ->willReturn($widgetDefinition);
        $widgetDefinition
            ->expects($this->once())
            ->method('getClass')
            ->willReturn(Time::class);

        $widgetFactory
            ->expects($this->once())
            ->method('setArguments')
            ->with([
                [
                    Time::TYPE => new Reference($widgetId)
                ]
            ]);

        $this->subject->process($this->container);
    }
}
