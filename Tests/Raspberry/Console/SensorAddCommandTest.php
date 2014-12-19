<?php

namespace Tests\Raspberry\Console\SensorAddCommand;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Console\SensorAddCommand;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\Sensors\SensorInterface;
use Raspberry\Sensors\SensorVO;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\Console\SensorAddCommand
 */
class SensorAddCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SensorAddCommand
     */
    private $subject;

    /**
     * @var SensorGateway|MockObject
     */
    private $mockSensorGateway;

    /**
     * @var SensorBuilder|MockObject
     */
    private $mockSensorBuilder;

    public function setUp()
    {
        $this->mockSensorGateway = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->mockSensorBuilder = $this->getMock(SensorBuilder::class, [], [], '', false);

        $this->subject = new SensorAddCommand($this->mockSensorGateway, $this->mockSensorBuilder);
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteWhenSensorNotSupportedAndAbort()
    {
        $application = new Application();
        $application->add($this->subject);
        $command_tester = new CommandTester($this->subject);

        $sensor_1 = $this->getMock(SensorInterface::class);
        $sensor_2 = $this->getMock(SensorInterface::class);

        $sensors = [
        $sensor_type_1 = 'type_1' => $sensor_1,
        $sensor_type_2 = 'type_2' => $sensor_2,
        ];

        $this->mockSensorBuilder
        ->expects($this->once())
        ->method('getSensors')
        ->will($this->returnValue($sensors));

        /** @var HelperSet|MockObject $helper_set */
        $helper_set = $this->getMock(HelperSet::class);
        $this->subject->setHelperSet($helper_set);

        /** @var DialogHelper|MockObject $helper_set */
        $dialog = $this->getMock(DialogHelper::class);

        $helper_set
        ->expects($this->once())
        ->method('get')
        ->with('dialog')
        ->will($this->returnValue($dialog));

        $dialog
        ->expects($this->at(0))
        ->method('select')
        ->with($this->isInstanceOf(OutputInterface::class), "Sensor type?\n", array_keys($sensors))
        ->will($this->returnValue(1));

        $dialog
        ->expects($this->at(1))
        ->method('askConfirmation')
        ->with($this->isInstanceOf(OutputInterface::class), 'Abort adding this sensor? (y/n)')
        ->will($this->returnValue(true));

        $sensor_2
        ->expects($this->once())
        ->method('isSupported')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue(false));

        $input = ['--force'];
        $command_tester->execute($input);
    }

    public function testExecuteWhenSensorNotSupported()
    {
        $application = new Application();
        $application->add($this->subject);
        $command_tester = new CommandTester($this->subject);

        $sensor_1 = $this->getMock(SensorInterface::class);
        $sensor_2 = $this->getMock(SensorInterface::class);

        $sensors = [
        $sensor_type_1 = 'type_1' => $sensor_1,
        $sensor_type_2 = 'type_2' => $sensor_2,
        ];

        $this->mockSensorBuilder
        ->expects($this->once())
        ->method('getSensors')
        ->will($this->returnValue($sensors));

        /** @var HelperSet|MockObject $helper_set */
        $helper_set = $this->getMock(HelperSet::class);
        $this->subject->setHelperSet($helper_set);

        /** @var DialogHelper|MockObject $helper_set */
        $dialog = $this->getMock(DialogHelper::class);

        $name        = 'name';
        $description = 'description';
        $pin         = 'pin';
        $interval    = 12;
        $node        = 2;
        $value       = 122;
        $formatted_value = "122 Grad";

        $helper_set
        ->expects($this->once())
        ->method('get')
        ->with('dialog')
        ->will($this->returnValue($dialog));

        $dialog
        ->expects($this->at(0))
        ->method('select')
        ->with($this->isInstanceOf(OutputInterface::class), "Sensor type?\n", array_keys($sensors))
        ->will($this->returnValue(1));

        $dialog
        ->expects($this->at(1))
        ->method('askConfirmation')
        ->with($this->isInstanceOf(OutputInterface::class), 'Abort adding this sensor? (y/n)')
        ->will($this->returnValue(false));

        $dialog
        ->expects($this->at(2))
        ->method('ask')
        ->with($this->isInstanceOf(OutputInterface::class), "Sensor name\n")
        ->will($this->returnValue($name));

        $dialog
        ->expects($this->at(3))
        ->method('ask')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue($description));

        $dialog
        ->expects($this->at(4))
        ->method('ask')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue($pin));

        $dialog
        ->expects($this->at(5))
        ->method('ask')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue($interval));

        $dialog
        ->expects($this->at(6))
        ->method('ask')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue($node));

        $sensor_2
        ->expects($this->once())
        ->method('isSupported')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue(false));

        $sensor_2
        ->expects($this->once())
        ->method('getValue')
        ->with($pin)
        ->will($this->returnValue($value));

        $sensor_2
        ->expects($this->once())
        ->method('formatValue')
        ->with($value)
        ->will($this->returnValue($formatted_value));


        $expected_sensor_vo = new SensorVO();
        $expected_sensor_vo->name = $name;
        $expected_sensor_vo->type = $sensor_type_2;
        $expected_sensor_vo->description = $description;
        $expected_sensor_vo->pin = $pin;
        $expected_sensor_vo->interval = $interval;
        $expected_sensor_vo->node = $node;

        $this->mockSensorGateway
        ->expects($this->once())
        ->method('addSensor')
        ->with($expected_sensor_vo);

        $input = ['--force'];
        $command_tester->execute($input);

        $display = $command_tester->getDisplay();
        $this->assertEquals("Sensor is not supported\nSensor value: 122 Grad\n", $display);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);
        $command_tester = new CommandTester($this->subject);

        $sensor_1 = $this->getMock(SensorInterface::class);
        $sensor_2 = $this->getMock(SensorInterface::class);

        $sensors = [
        $sensor_type_1 = 'type_1' => $sensor_1,
        $sensor_type_2 = 'type_2' => $sensor_2,
        ];

        $this->mockSensorBuilder
        ->expects($this->once())
        ->method('getSensors')
        ->will($this->returnValue($sensors));

        /** @var HelperSet|MockObject $helper_set */
        $helper_set = $this->getMock(HelperSet::class);
        $this->subject->setHelperSet($helper_set);

        /** @var DialogHelper|MockObject $helper_set */
        $dialog = $this->getMock(DialogHelper::class);

        $name        = 'name';
        $description = 'description';
        $pin         = 'pin';
        $interval    = 12;
        $node        = 2;
        $value       = 122;
        $formatted_value = "122 Grad";

        $helper_set
        ->expects($this->once())
        ->method('get')
        ->with('dialog')
        ->will($this->returnValue($dialog));

        $dialog
        ->expects($this->at(0))
        ->method('select')
        ->with($this->isInstanceOf(OutputInterface::class), "Sensor type?\n", array_keys($sensors))
        ->will($this->returnValue(1));

        $dialog
        ->expects($this->at(1))
        ->method('ask')
        ->with($this->isInstanceOf(OutputInterface::class), "Sensor name\n")
        ->will($this->returnValue($name));

        $dialog
        ->expects($this->at(2))
        ->method('ask')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue($description));

        $dialog
        ->expects($this->at(3))
        ->method('ask')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue($pin));

        $dialog
        ->expects($this->at(4))
        ->method('ask')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue($interval));

        $dialog
        ->expects($this->at(5))
        ->method('ask')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue($node));

        $sensor_2
        ->expects($this->once())
        ->method('isSupported')
        ->with($this->isInstanceOf(OutputInterface::class))
        ->will($this->returnValue(true));

        $sensor_2
        ->expects($this->once())
        ->method('getValue')
        ->with($pin)
        ->will($this->returnValue(null));

        $expected_sensor_vo = new SensorVO();
        $expected_sensor_vo->name = $name;
        $expected_sensor_vo->type = $sensor_type_2;
        $expected_sensor_vo->description = $description;
        $expected_sensor_vo->pin = $pin;
        $expected_sensor_vo->interval = $interval;
        $expected_sensor_vo->node = $node;

        $this->mockSensorGateway
        ->expects($this->once())
        ->method('addSensor')
        ->with($expected_sensor_vo);

        $input = ['--force'];
        $command_tester->execute($input);

        $display = $command_tester->getDisplay();
        $this->assertEquals("Sensor is supported\nSensor returned invalid data.\n", $display);
    }
}
