<?php

namespace Homie\Tests\Sensors;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SensorBuilderTest extends TestCase
{

    /**
     * @var SensorBuilder
     */
    private $subject;

    /**
     * @var ServiceLocator|MockObject
     */
    private $serviceLocator;

    public function setUp()
    {
        $this->serviceLocator = $this->createMock(ServiceLocator::class);

        $this->subject = new SensorBuilder(
            $this->serviceLocator,
            []
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testBuildInvalid()
    {
        $this->serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with('sensor_123')
            ->willThrowException(new ServiceNotFoundException('sensor_123'));

        $sensorType = 'sensor_123';

        $this->subject->build($sensorType);
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
