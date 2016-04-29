<?php

namespace Tests\Homie\Sensors\Command;

use Homie\Sensors\DeleteOldValues;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Command\CleanCron;
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
     * @var DeleteOldValues|MockObject
     */
    private $deleteOldValues;

    /**
     * @var SensorGateway|MockObject
     */
    private $gateway;

    public function setUp()
    {
        $this->deleteOldValues = $this->getMock(DeleteOldValues::class, [], [], '', false);
        $this->gateway         = $this->getMock(SensorGateway::class, [], [], '', false);

        $this->subject = new CleanCron(
            $this->deleteOldValues,
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

        $this->deleteOldValues
            ->expects($this->at(0))
            ->method('deleteValues')
            ->with($sensorId)
            ->willReturn(5);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertEquals("sensor #10, deleted 5 rows\ndone\n", $output);
    }
}
