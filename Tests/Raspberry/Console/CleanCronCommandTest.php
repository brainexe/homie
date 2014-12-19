<?php

namespace Tests\Raspberry\Console\CleanCronCommand;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Console\CleanCronCommand;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\SensorGateway;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\Console\CleanCronCommand
 */
class CleanCronCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var CleanCronCommand
     */
    private $subject;

    /**
     * @var SensorValuesGateway|MockObject
     */
    private $mockSensorValuesGateway;

    /**
     * @var SensorGateway|MockObject
     */
    private $mockSensorGateway;

    /**
     * @var array
     */
    private $deleteSensorValues = [];

    public function setUp()
    {
        $this->mockSensorValuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->mockSensorGateway       = $this->getMock(SensorGateway::class, [], [], '', false);

        $this->subject = new CleanCronCommand(
            $this->mockSensorValuesGateway,
            $this->mockSensorGateway,
            $this->deleteSensorValues = [
            [
            'days'       => 7,
            'percentage' => 10,
            ],
            [
            'days'       => 10,
            'percentage' => 80,
            ]
            ]
        );
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $sensor_ids = [
        $sensor_id = 10
        ];

        $this->mockSensorGateway
        ->expects($this->once())
        ->method('getSensorIds')
        ->will($this->returnValue($sensor_ids));

        $this->mockSensorValuesGateway
        ->expects($this->at(0))
        ->method('deleteOldValues')
        ->with($sensor_id, 7, 10)
        ->will($this->returnValue(5));

        $this->mockSensorValuesGateway
        ->expects($this->at(1))
        ->method('deleteOldValues')
        ->with($sensor_id, 10, 80)
        ->will($this->returnValue(8));

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertEquals("sensor #10, deleted 13 rows\ndone\n", $output);
    }
}
