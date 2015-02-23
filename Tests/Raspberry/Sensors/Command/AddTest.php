<?php

namespace Tests\Raspberry\Sensors\Command;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Raspberry\Sensors\Command\Add;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\Sensors\SensorInterface;
use Raspberry\Sensors\SensorVO;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\Sensors\Command\Add
 */
class AddTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Add
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
        $this->gateway = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->builder = $this->getMock(SensorBuilder::class, [], [], '', false);

        $this->subject = new Add(
            $this->gateway,
            $this->builder
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteWhenSensorNotSupportedAndAbort()
    {
        $application = new Application();
        $application->add($this->subject);
        $tester = new CommandTester($this->subject);

        $sensor1 = $this->getMock(SensorInterface::class);
        $sensor2 = $this->getMock(SensorInterface::class);

        $sensors = [
            $sensorType1 = 'type_1' => $sensor1,
                'type_2' => $sensor2,
        ];

        $output = $this->isInstanceOf(OutputInterface::class);
        $input  = $this->isInstanceOf(InputInterface::class);

        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        /** @var HelperSet|MockObject $helperSet */
        $helperSet = $this->getMock(HelperSet::class);
        $this->subject->setHelperSet($helperSet);

        /** @var QuestionHelper|MockObject $helper_set */
        $questionHelper = $this->getMock(QuestionHelper::class);

        $helperSet
            ->expects($this->once())
            ->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $questionHelper
            ->expects($this->at(0))
            ->method('ask')
            ->with(
                $input,
                $output,
                new ChoiceQuestion("Sensor Type", array_keys($sensors))
            )
            ->willReturn($sensorType1);

        $questionHelper
            ->expects($this->at(1))
            ->method('ask')
            ->with(
                $input,
                $output,
                $this->isInstanceOf(Question::class)
            )
            ->willReturn(true);

        $sensor1
            ->expects($this->once())
            ->method('isSupported')
            ->with($output)
            ->willReturn(false);

        $input = ['--force'];
        $tester->execute($input);
    }

    public function testExecuteWhenSensorNotSupported()
    {
        $application = new Application();
        $application->add($this->subject);
        $commandTester = new CommandTester($this->subject);

        $sensor1 = $this->getMock(SensorInterface::class);
        $sensor2 = $this->getMock(SensorInterface::class);

        $sensors = [
            'type_1' => $sensor1,
            $sensorType2 = 'type_2' => $sensor2,
        ];

        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        /** @var HelperSet|MockObject $helperSet */
        $helperSet = $this->getMock(HelperSet::class);
        $this->subject->setHelperSet($helperSet);

        /** @var QuestionHelper|MockObject $helper_set */
        $helper = $this->getMock(QuestionHelper::class);

        $output = $this->isInstanceOf(OutputInterface::class);
        $input  = $this->isInstanceOf(InputInterface::class);

        $name        = 'name';
        $description = 'description';
        $pin         = 'pin';
        $interval    = 12;
        $node        = 2;
        $value       = 122;
        $formattedValue = "122 Grad";

        $helperSet
            ->expects($this->once())
            ->method('get')
            ->with('question')
            ->willReturn($helper);

        $helper
            ->expects($this->at(0))
            ->method('ask')
            ->with(
                $input,
                $output,
                new ChoiceQuestion("Sensor Type", array_keys($sensors))
            )
            ->willReturn($sensorType2);

        $helper
            ->expects($this->at(1))
            ->method('ask')
            ->with(
                $input,
                $output,
                $this->isInstanceOf(Question::class)
            );

        $helper
            ->expects($this->at(2))
            ->method('ask')
            ->with($input, $output, new Question("Sensor name?\n"))
            ->willReturn($name);

        $helper
            ->expects($this->at(3))
            ->method('ask')
            ->with($input, $output, new Question("Description (optional)?\n"))
            ->willReturn($description);

        $helper
            ->expects($this->at(4))
            ->method('ask')
            ->with($input, $output, new Question("Pin (Optional)?\n"))
            ->willReturn($pin);

        $helper
            ->expects($this->at(5))
            ->method('ask')
            ->with($input, $output, new Question("Interval in minutes\n"))
            ->willReturn($interval);

        $helper
            ->expects($this->at(6))
            ->method('ask')
            ->with($input, $output, new Question("Node\n"))
            ->willReturn($node);

        $sensor2
            ->expects($this->once())
            ->method('isSupported')
            ->with($output)
            ->willReturn(false);

        $sensor2
            ->expects($this->once())
            ->method('getValue')
            ->with($pin)
            ->willReturn($value);

        $sensor2
            ->expects($this->once())
            ->method('formatValue')
            ->with($value)
            ->willReturn($formattedValue);

        $expectedVo              = new SensorVO();
        $expectedVo->name        = $name;
        $expectedVo->type        = $sensorType2;
        $expectedVo->description = $description;
        $expectedVo->pin         = $pin;
        $expectedVo->interval    = $interval;
        $expectedVo->node        = $node;

        $this->gateway
            ->expects($this->once())
            ->method('addSensor')
            ->with($expectedVo);

        $input = ['--force'];
        $commandTester->execute($input);

        $display = $commandTester->getDisplay();
        $this->assertEquals("Sensor is not supported\nSensor value: 122 Grad\n", $display);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);
        $commandTester = new CommandTester($this->subject);

        $sensor1 = $this->getMock(SensorInterface::class);
        $sensor2 = $this->getMock(SensorInterface::class);

        $sensors = [
                'type_1' => $sensor1,
            $sensorType2 = 'type_2' => $sensor2,
        ];

        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        /** @var HelperSet|MockObject $helperSet */
        $helperSet = $this->getMock(HelperSet::class);
        $this->subject->setHelperSet($helperSet);

        /** @var QuestionHelper|MockObject $helper_set */
        $helper = $this->getMock(QuestionHelper::class);

        $name        = 'name';
        $description = 'description';
        $pin         = 'pin';
        $interval    = 12;
        $node        = 2;

        $helperSet
            ->expects($this->once())
            ->method('get')
            ->with('question')
            ->willReturn($helper);

        $output = $this->isInstanceOf(OutputInterface::class);
        $input  = $this->isInstanceOf(InputInterface::class);

        $helper
            ->expects($this->at(0))
            ->method('ask')
            ->with(
                $input,
                $output,
                new ChoiceQuestion("Sensor Type", array_keys($sensors))
            )
            ->willReturn($sensorType2);

        $helper
            ->expects($this->at(1))
            ->method('ask')
            ->with($input, $output, new Question("Sensor name?\n"))
            ->willReturn($name);

        $helper
            ->expects($this->at(2))
            ->method('ask')
            ->with($input, $output, new Question("Description (optional)?\n"))
            ->willReturn($description);

        $helper
            ->expects($this->at(3))
            ->method('ask')
            ->with($input, $output, new Question("Pin (Optional)?\n"))
            ->willReturn($pin);

        $helper
            ->expects($this->at(4))
            ->method('ask')
            ->with($input, $output, new Question("Interval in minutes\n"))
            ->willReturn($interval);

        $helper
            ->expects($this->at(5))
            ->method('ask')
            ->with($input, $output, new Question("Node\n"))
            ->willReturn($node);

        $sensor2
            ->expects($this->once())
            ->method('isSupported')
            ->with($output)
            ->willReturn(true);

        $sensor2
            ->expects($this->once())
            ->method('getValue')
            ->with($pin)
            ->willReturn(null);

        $expectedVo              = new SensorVO();
        $expectedVo->name        = $name;
        $expectedVo->type        = $sensorType2;
        $expectedVo->description = $description;
        $expectedVo->pin         = $pin;
        $expectedVo->interval    = $interval;
        $expectedVo->node        = $node;

        $this->gateway
            ->expects($this->once())
            ->method('addSensor')
            ->with($expectedVo);

        $input = ['--force'];
        $commandTester->execute($input);

        $display = $commandTester->getDisplay();
        $this->assertEquals(
            "Sensor is supported\nSensor returned invalid data.\n",
            $display
        );
    }
}
