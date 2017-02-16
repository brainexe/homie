<?php

namespace Homie\Tests\Sensors;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SensorBuilderTest extends TestCase
{

    /**
     * @var SensorBuilder
     */
    private $subject;

    /**
     * @var ContainerInterface|MockObject
     */
    private $container;

    public function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);

        $this->subject = new SensorBuilder($this->container);
    }

    public function testGetSensors()
    {
        /** @var Sensor|MockObject $sensor */
        $sensor = $this->createMock(Sensor::class);
        $sensorType = 'sensor_123';
        $serviceId = '__sensor';

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with($serviceId)
            ->willReturn($sensor);

        $this->subject->addSensor($sensorType, $serviceId);
        $actualResult = $this->subject->getSensors();

        $this->assertEquals([$sensorType => $sensor], $actualResult);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid sensor type: sensor_123
     */
    public function testBuildInvalid()
    {
        $sensorType = 'sensor_123';

        $this->subject->build($sensorType);
    }

    public function testBuildValid()
    {
        /** @var Sensor|MockObject $sensorMock */
        $sensorMock = $this->createMock(Sensor::class);
        $sensorType = 'sensor_123';
        $serviceId = '__serviceid';

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with($serviceId)
            ->willReturn($sensorMock);

        $this->subject->addSensor($sensorType, $serviceId);

        $actual = $this->subject->build($sensorType);
        $this->assertEquals($sensorMock, $actual);

        $actual = $this->subject->build($sensorType);
        $this->assertEquals($sensorMock, $actual);
    }

    public function testGetFormatter()
    {
        $type = 'mockType';

        /** @var Formatter $formatter */
        $formatter = $this->createMock(Formatter::class);

        $this->subject->addFormatter($type, $formatter);

        $actual = $this->subject->getFormatter($type);

        $this->assertEquals($formatter, $actual);
    }

    public function testGetFormatterDefault()
    {
        $type = 'mockType';

        /** @var Formatter $formatter */
        $formatter = $this->createMock(Formatter::class);
        $this->subject->addFormatter(None::TYPE, $formatter);

        $actual = $this->subject->getFormatter($type);

        $this->assertEquals($formatter, $actual);
    }
}
