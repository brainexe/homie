<?php

namespace Tests\Homie\Sensors\Command;

use Homie\Sensors\Command\Crawl;
use Homie\Sensors\Interfaces\Searchable;
use Homie\Sensors\Interfaces\Sensor;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorBuilder;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

abstract class SearchableTestSensor2 implements Sensor, Searchable {
}

/**
 * @covers \Homie\Sensors\Command\Crawl
 */
class CrawlTest extends TestCase
{

    /**
     * @var Crawl
     */
    private $subject;

    /**
     * @var SensorGateway|MockObject
     */
    private $gateway;

    /**
     * @var SensorBuilder|MockObject
     */
    private $builder;

    public function setUp()
    {
        $this->gateway = $this->createMock(SensorGateway::class);
        $this->builder = $this->createMock(SensorBuilder::class);

        $this->subject = new Crawl(
            $this->gateway,
            $this->builder
        );
    }

    public function testExecuteWithExisting()
    {
        $sensors = [
            ['type' => 'otherType'],
            ['type' => 'myType']
        ];

        $sensorModels = [
            $sensorModel = $this->createMock(Sensor::class)
        ];

        $sensorModel
            ->expects($this->exactly(2))
            ->method('getSensorType')
            ->willReturn($sensorType = 'myType');

        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorModels);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        $application = new Application();
        $application->add($this->subject);
        $input = [];
        $commandTester = new CommandTester($this->subject);
        $commandTester->execute($input);

        $this->assertEquals("Handling myType...
Sensor \"myType\" with parameter \"\" already exists", trim($commandTester->getDisplay()));
    }

    public function testExecuteWithSearchableWithoutParameters()
    {
        $sensors = [
            ['type' => 'otherType'],
            ['type' => 'otherType2'],
        ];

        $sensorModels = [
            $sensorModel = $this->createMock(SearchableTestSensor2::class)
        ];

        $sensorModel
            ->expects($this->exactly(2))
            ->method('getSensorType')
            ->willReturn($sensorType = 'myType');

        $sensorModel
            ->expects($this->once())
            ->method('search')
            ->willReturn($parameters = []);

        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorModels);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        $application = new Application();
        $application->add($this->subject);
        $input = [];
        $commandTester = new CommandTester($this->subject);
        $commandTester->execute($input);

        $this->assertEquals("Handling myType...
Searching...
No valid sensor found for myType...", trim($commandTester->getDisplay()));
    }

    public function testExecuteWithValidParameter()
    {
        $sensors = [
            ['type' => 'otherType'],
            ['type' => 'otherType2'],
        ];

        $sensorModels = [
            $sensorModel = $this->createMock(SearchableTestSensor2::class)
        ];
        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorModels);
        $sensorModel
            ->expects($this->once())
            ->method('search')
            ->willReturn($parameters = [
                'myParameter'
            ]);
        $sensorModel
            ->expects($this->exactly(2))
            ->method('getSensorType')
            ->willReturn($sensorType = 'myType');

        $application = new Application();
        $application->add($this->subject);
        $input = [];
        $commandTester = new CommandTester($this->subject);
        $commandTester->execute($input, ['interactive' => false]);

        $this->assertEquals("Handling myType...
Searching...", trim($commandTester->getDisplay()));
    }
}
