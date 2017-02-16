<?php

namespace Tests\Homie\Sensors\CompilerPass;

use Homie\Sensors\CompilerPass\SensorFormatter;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\SensorBuilder;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers \Homie\Sensors\CompilerPass\SensorFormatter
 */
class FormatterPassTest extends TestCase
{

    /**
     * @var SensorFormatter|MockObject
     */
    private $subject;

    /**
     * @var MockObject|ContainerBuilder
     */
    private $container;

    public function setUp()
    {
        $this->subject = $this->getMockBuilder(SensorFormatter::class)
                              ->setMethods(['dumpVariableToCache'])
                              ->getMock();

        $this->container = $this->createMock(ContainerBuilder::class);
    }

    public function testProcess()
    {
        $sensorBuilder    = $this->createMock(Definition::class);
        $sensorDefinition = $this->createMock(Definition::class);
        $formatter        = $this->createMock(Formatter::class);
        $sensorId         = 'sensor_1';

        $this->container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with(SensorBuilder::class)
            ->willReturn($sensorBuilder);

        $this->container
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(SensorFormatter::TAG)
            ->will($this->returnValue([
                $sensorId => $sensorDefinition
            ]));

        $this->container
            ->expects($this->at(2))
            ->method('get')
            ->with($sensorId)
            ->willReturn($formatter);

        $formatter->expects($this->once())
             ->method('getType')
             ->willReturn($sensorId);

        $sensorBuilder
            ->expects($this->once())
            ->method('addMethodCall')
            ->with('addFormatter', [$sensorId, new Reference($sensorId)]);

        $this->subject
            ->expects($this->once())
            ->method('dumpVariableToCache')
            ->with('sensor_formatter', [
                0 => $sensorId
            ]);

        $this->subject->process($this->container);
    }
}
