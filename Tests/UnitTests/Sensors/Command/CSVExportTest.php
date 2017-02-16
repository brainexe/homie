<?php

namespace Tests\Homie\Sensors\Command;

use ArrayIterator;
use BrainExe\Core\Util\Time;
use Homie\Sensors\Command\CSVExport;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\SensorValuesGateway;
use Homie\Sensors\SensorGateway;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Homie\Sensors\Command\CSVExport
 */
class CSVExportTest extends TestCase
{

    /**
     * @var CSVExport
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
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->valuesGateway = $this->createMock(SensorValuesGateway::class);
        $this->gateway       = $this->createMock(SensorGateway::class);
        $this->time          = $this->createMock(Time::class);

        $this->subject = new CSVExport(
            $this->gateway,
            $this->valuesGateway
        );

        $this->subject->setTime($this->time);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $sensor = [
            'name' => 'myName',
            'sensorId' => '12',
            'interval' => 1,
        ];

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn([$sensor]);

        $this->valuesGateway
            ->expects($this->any())
            ->method('getByTime')
            ->with(['12'])
            ->willReturn(new ArrayIterator([100]));

        $tmpFile = tempnam(sys_get_temp_dir(), 'test');

        $commandTester->execute(['file' => $tmpFile]);

        $output = $commandTester->getDisplay();

        $this->assertEquals("Done\n", $output);
    }
}
