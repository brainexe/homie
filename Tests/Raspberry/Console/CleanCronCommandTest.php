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
    private $_subject;

    /**
     * @var SensorValuesGateway|MockObject
     */
    private $_mockSensorValuesGateway;

    /**
     * @var SensorGateway|MockObject
     */
    private $_mockSensorGateway;

    /**
     * @var array
     */
    private $_delete_sensor_values = [];

    public function setUp()
    {
        $this->_mockSensorValuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->_mockSensorGateway       = $this->getMock(SensorGateway::class, [], [], '', false);

        $this->_subject = new CleanCronCommand(
            $this->_mockSensorValuesGateway,
            $this->_mockSensorGateway,
            $this->_delete_sensor_values = [
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
        $application->add($this->_subject);

        $commandTester = new CommandTester($this->_subject);

        $sensor_ids = [
        $sensor_id = 10
        ];

        $this->_mockSensorGateway
        ->expects($this->once())
        ->method('getSensorIds')
        ->will($this->returnValue($sensor_ids));

        $this->_mockSensorValuesGateway
        ->expects($this->at(0))
        ->method('deleteOldValues')
        ->with($sensor_id, 7, 10)
        ->will($this->returnValue(5));

        $this->_mockSensorValuesGateway
        ->expects($this->at(1))
        ->method('deleteOldValues')
        ->with($sensor_id, 10, 80)
        ->will($this->returnValue(8));

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertEquals("sensor #10, deleted 13 rows\ndone\n", $output);
    }
}
