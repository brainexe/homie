<?php

namespace Tests\Homie\Sensors\Command;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Command\CleanCron;
use Homie\Sensors\SensorValuesGateway;
use Homie\Sensors\SensorGateway;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Homie\Sensors\Command\CleanCron
 */
class CleanCronTest extends TestCase
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

    public function setUp()
    {
        $this->valuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->gateway       = $this->getMock(SensorGateway::class, [], [], '', false);

        $this->subject = new CleanCron(
            $this->valuesGateway,
            $this->gateway
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
            ->with($sensorId)
            ->willReturn(5);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertEquals("sensor #10, deleted 5 rows\ndone\n", $output);
    }
}
