<?php

namespace Tests\Raspberry\Sensors\Command;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\Command\CleanCron;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\SensorGateway;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Raspberry\Sensors\Command\CleanCron
 */
class CleanCronTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var CleanCron
     */
    private $subject;

    /**
     * @var SensorValuesGateway|MockObject
     */
    private $valuesGateway;

    /**
     * @var SensorGateway|MockObject
     */
    private $gateway;

    /**
     * @var array
     */
    private $deleteSensorValues = [];

    public function setUp()
    {
        $this->valuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->gateway       = $this->getMock(SensorGateway::class, [], [], '', false);

        $this->subject = new CleanCron(
            $this->valuesGateway,
            $this->gateway,
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

        $sensorIds = [
            $sensorId = 10
        ];

        $this->gateway
            ->expects($this->once())
            ->method('getSensorIds')
            ->willReturn($sensorIds);

        $this->valuesGateway
            ->expects($this->at(0))
            ->method('deleteOldValues')
            ->with($sensorId, 7, 10)
            ->willReturn(5);

        $this->valuesGateway
            ->expects($this->at(1))
            ->method('deleteOldValues')
            ->with($sensorId, 10, 80)
            ->willReturn(8);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertEquals("sensor #10, deleted 13 rows\ndone\n", $output);
    }
}
