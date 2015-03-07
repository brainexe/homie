<?php

namespace Tests\Raspberry\Sensors;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\CompilerPass;
use Raspberry\Sensors\Interfaces\Sensor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @Covers Raspberry\Sensors\CompilerPass
 */
class CompilerPassTest extends TestCase
{

    /**
     * @var CompilerPass
     */
    private $subject;

    /**
     * @var MockObject|ContainerBuilder
     */
    private $mockContainer;

    public function setUp()
    {
        $this->subject = new CompilerPass();
        $this->mockContainer = $this->getMock(ContainerBuilder::class);
    }

    /**
     *
     */
    public function testProcess()
    {
        $sensorBuilder    = $this->getMock(Definition::class);
        $sensorDefinition = $this->getMock(Definition::class);
        $sensor           = $this->getMock(Sensor::class);
        $sensorId         = 'sensor_1';

        $this->mockContainer
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('SensorBuilder')
            ->willReturn($sensorBuilder);

        $this->mockContainer
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(CompilerPass::TAG)
            ->will($this->returnValue([
                $sensorId => $sensorDefinition
            ]));

        $this->mockContainer
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

        $this->subject->process($this->mockContainer);
    }
}
