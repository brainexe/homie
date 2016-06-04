<?php

namespace Tests\Homie\Sensors\CompilerPass;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\CompilerPass;
use Homie\Sensors\CompilerPass\Sensor;
use Homie\Sensors\Interfaces\Sensor as SensorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers Homie\Sensors\CompilerPass\Sensor
 */
class CompilerPassTest extends TestCase
{

    /**
     * @var Sensor
     */
    private $subject;

    /**
     * @var MockObject|ContainerBuilder
     */
    private $container;

    public function setUp()
    {
        $this->subject   = new Sensor();
        $this->container = $this->createMock(ContainerBuilder::class);
    }

    public function testProcess()
    {
        $sensorBuilder    = $this->createMock(Definition::class);
        $sensorDefinition = $this->createMock(Definition::class);
        $sensor           = $this->createMock(SensorInterface::class);
        $sensorId         = 'sensor_1';

        $this->container
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('SensorBuilder')
            ->willReturn($sensorBuilder);

        $this->container
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(Sensor::TAG)
            ->will($this->returnValue([
                $sensorId => $sensorDefinition
            ]));

        $this->container
            ->expects($this->at(2))
            ->method('get')
            ->with($sensorId)
            ->willReturn($sensor);

        $sensor->expects($this->once())
             ->method('getSensorType')
             ->willReturn($sensorId);

        $sensorBuilder
            ->expects($this->once())
            ->method('addMethodCall')
            ->with('addSensor', [$sensorId, new Reference($sensorId)]);

        $this->subject->process($this->container);
    }
}
