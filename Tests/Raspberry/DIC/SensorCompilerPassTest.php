<?php

namespace Tests\Raspberry\DIC\SensorCompilerPass;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\DIC\SensorCompilerPass;
use Raspberry\Sensors\Sensors\SensorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @Covers Raspberry\DIC\SensorCompilerPass
 */
class SensorCompilerPassTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SensorCompilerPass
     */
    private $subject;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    private $mock_container;

    public function setUp()
    {
        $this->subject = new SensorCompilerPass();
        $this->mock_container = $this->getMock(ContainerBuilder::class);
    }

    /**
     *
     */
    public function testProcess()
    {
        $sensor_builder = $this->getMock(Definition::class);
        $sensor_definition = $this->getMock(Definition::class);
        $sensorId = 'sensor_1';

        $sensor = $this->getMock(SensorInterface::class);

        $this->mock_container
        ->expects($this->at(0))
        ->method('getDefinition')
        ->with('SensorBuilder')
        ->will($this->returnValue($sensor_builder));

        $this->mock_container
        ->expects($this->at(1))
        ->method('findTaggedServiceIds')
        ->with(SensorCompilerPass::TAG)
        ->will($this->returnValue([
        $sensorId => $sensor_definition
        ]));

        $this->mock_container
        ->expects($this->at(2))
        ->method('get')
        ->with($sensorId)
        ->will($this->returnValue($sensor));

        $sensor->expects($this->once())
         ->method('getSensorType')
         ->will($this->returnValue($sensorId));

        $sensor_builder
        ->expects($this->once())
        ->method('addMethodCall')
        ->with('addSensor', [$sensorId, new Reference($sensorId)]);

        $this->subject->process($this->mock_container);
    }
}
