<?php

namespace Tests\Raspberry\Sensors\CompilerPass;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\CompilerPass;
use Raspberry\Sensors\CompilerPass\Sensor;
use Raspberry\Sensors\Interfaces\Sensor as SensorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers Raspberry\Sensors\CompilerPass\Sensor
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
        $this->container = $this->getMock(ContainerBuilder::class);
    }

    public function testProcess()
    {
        $sensorBuilder    = $this->getMock(Definition::class);
        $sensorDefinition = $this->getMock(Definition::class);
        $sensor           = $this->getMock(SensorInterface::class);
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
