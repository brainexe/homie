<?php

namespace Tests\Raspberry\Sensors\Command;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Raspberry\Sensors\Command\Add;
use Raspberry\Sensors\Interfaces\Parameterized;
use Raspberry\Sensors\Interfaces\Sensor;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorVO;
use SebastianBergmann\RecursionContext\Exception;
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
     * @expectedException Exception
     * @expectedExceptionMessage Parameter "mockparameter" is not supported
     */
    public function testExecuteWhenSensorNotSupportedAndAbort()
    {
        $application = new Application();
        $application->add($this->subject);
        $tester = new CommandTester($this->subject);

        $sensor1 = $this->getMock(Parameterized::class);
        $sensor2 = $this->getMock(Parameterized::class);

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

        $parameter = 'mockparameter';
        $questionHelper
            ->expects($this->at(1))
            ->method('ask')
            ->with($input, $output, new Question("Parameter (Optional)?\n"))
            ->willReturn($parameter);

        $sensor1
            ->expects($this->once())
            ->method('isSupported')
            ->with($parameter, $output)
            ->willReturn(false);

        $input = ['--force'];
        $tester->execute($input);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);
        $commandTester = new CommandTester($this->subject);

        $sensor1 = $this->getMock(Parameterized::class);
        $sensor2 = $this->getMock(Parameterized::class);

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

        $sensor2
            ->expects($this->exactly(2))
            ->method('getSensorType')
            ->willReturn('type_2');

        /** @var QuestionHelper|MockObject $helper_set */
        $helper = $this->getMock(QuestionHelper::class);

        $name        = 'name';
        $description = 'description';
        $parameter   = 'pin';
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
            ->with($input, $output, new Question("Parameter (Optional)?\n"))
            ->willReturn($parameter);

        $helper
            ->expects($this->at(2))
            ->method('ask')
            ->with($input, $output, new Question("Sensor name?\n", $sensorType2))
            ->willReturn($name);

        $helper
            ->expects($this->at(3))
            ->method('ask')
            ->with($input, $output, new Question("Description (optional)?\n"))
            ->willReturn($description);

        $helper
            ->expects($this->at(4))
            ->method('ask')
            ->with($input, $output, new Question("Interval in minutes\n", 5))
            ->willReturn($interval);

        $helper
            ->expects($this->at(5))
            ->method('ask')
            ->with($input, $output, new Question("Node\n"))
            ->willReturn($node);

        $sensor2
            ->expects($this->once())
            ->method('isSupported')
            ->with($parameter, $output)
            ->willReturn(true);

        $sensor2
            ->expects($this->once())
            ->method('getValue')
            ->with($parameter)
            ->willReturn(null);

        $expectedVo              = new SensorVO();
        $expectedVo->name        = $name;
        $expectedVo->type        = $sensorType2;
        $expectedVo->description = $description;
        $expectedVo->pin         = $parameter;
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
