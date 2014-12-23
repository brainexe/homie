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
     * @var MockObject|ContainerBuilder
     */
    private $mockContainer;

    public function setUp()
    {
        $this->subject = new SensorCompilerPass();
        $this->mockContainer = $this->getMock(ContainerBuilder::class);
    }

    /**
     *
     */
    public function testProcess()
    {
        $sensorBuilder    = $this->getMock(Definition::class);
        $sensorDefinition = $this->getMock(Definition::class);
        $sensor           = $this->getMock(SensorInterface::class);
        $sensorId         = 'sensor_1';

        $this->mockContainer
            ->expects($this->at(0))
            ->method('getDefinition')
            ->with('SensorBuilder')
            ->willReturn($sensorBuilder);

        $this->mockContainer
            ->expects($this->at(1))
            ->method('findTaggedServiceIds')
            ->with(SensorCompilerPass::TAG)
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
