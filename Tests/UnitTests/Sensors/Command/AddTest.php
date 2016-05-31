<?php

namespace Tests\Homie\Sensors\Command;

use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Interfaces\Searchable;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Sensors\Command\Add;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorVO;
use SebastianBergmann\RecursionContext\Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Tester\CommandTester;

abstract class TestSensorParameterized implements Sensor, Parameterized {}
abstract class SearchableTestSensor implements Sensor, Searchable {}

/**
 * @covers Homie\Sensors\Command\Add
 */
class AddTest extends TestCase
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

        $sensor1 = $this->getMock(TestSensorParameterized::class);
        $sensor2 = $this->getMock(TestSensorParameterized::class);

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
            ->with($input, $output, new Question("Parameter?\n"))
            ->willReturn($parameter);

        $sensor1
            ->expects($this->once())
            ->method('isSupported')
            ->with($this->isInstanceOf(SensorVO::class), $output)
            ->willReturn(false);

        $input = ['--force'];
        $tester->execute($input);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);
        $commandTester = new CommandTester($this->subject);

        $sensor1 = $this->getMock(TestSensorParameterized::class);
        $sensor2 = $this->getMock(TestSensorParameterized::class);

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
            ->expects($this->exactly(3))
            ->method('getSensorType')
            ->willReturn('type_2');

        $definition2 = new Definition();
        $definition2->formatter = 'formatter';

        $sensor2
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition2);

        /** @var QuestionHelper|MockObject $helper_set */
        $helper = $this->getMock(QuestionHelper::class);

        $name        = 'name';
        $description = 'description';
        $parameter   = 'myParameter';
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
            ->with($input, $output, new Question("Parameter?\n"))
            ->willReturn($parameter);

        $helper
            ->expects($this->at(2))
            ->method('ask')
            ->with($input, $output, new Question("Sensor name? (default: Type_2)\n", $sensorType2))
            ->willReturn($name);

        $helper
            ->expects($this->at(3))
            ->method('ask')
            ->with($input, $output, new Question("Description (optional)?\n"))
            ->willReturn($description);

        $helper
            ->expects($this->at(4))
            ->method('ask')
            ->with($input, $output, new Question("Interval in minutes (default: 5)\n", 5))
            ->willReturn($interval);

        $helper
            ->expects($this->at(5))
            ->method('ask')
            ->with($input, $output, new Question("Node? (only for advanced users needed)\n"))
            ->willReturn($node);

        $sensor2
            ->expects($this->once())
            ->method('isSupported')
            ->with($this->isInstanceOf(SensorVO::class), $output)
            ->willReturn(true);

        $expectedVo              = new SensorVO();
        $expectedVo->name        = $name;
        $expectedVo->type        = $sensorType2;
        $expectedVo->description = $description;
        $expectedVo->parameter   = $parameter;
        $expectedVo->interval    = $interval;
        $expectedVo->node        = $node;
        $expectedVo->color       = '#b06893';
        $expectedVo->formatter   = 'formatter';

        $sensor2
            ->expects($this->once())
            ->method('getValue')
            ->willThrowException(new InvalidSensorValueException($expectedVo, 'Invalid format xyz'));

        $this->gateway
            ->expects($this->once())
            ->method('addSensor')
            ->with($expectedVo);

        $input = ['--force'];
        $commandTester->execute($input);

        $display = $commandTester->getDisplay();
        $this->assertEquals(
            "Sensor is supported\nSensor returned invalid data: Invalid format xyz\n",
            $display
        );
    }

    public function testExecute2()
    {
        $application = new Application();
        $application->add($this->subject);
        $commandTester = new CommandTester($this->subject);

        $sensor1 = $this->getMock(TestSensorParameterized::class);
        $sensor2 = $this->getMock(Sensor::class);

        $definition2 = new Definition();
        $definition2->formatter = 'formatter';

        $sensor2
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition2);

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
            ->expects($this->exactly(1))
            ->method('getSensorType')
            ->willReturn('type_2');

        /** @var QuestionHelper|MockObject $helper_set */
        $helper = $this->getMock(QuestionHelper::class);

        $name        = 'name';
        $description = 'description';
        $parameter   = null;
        $interval    = 12;
        $node        = 2;

        $helperSet
            ->expects($this->once())
            ->method('get')
            ->with('question')
            ->willReturn($helper);

        $sensor2
            ->expects($this->once())
            ->method('getValue')
            ->willReturn(12);

        $formatter = $this->getMock(Formatter::class);
        $formatter
            ->expects($this->once())
            ->method('formatValue')
            ->with(12)
            ->willReturn('12°');

        $this->builder
            ->expects($this->once())
            ->method('getFormatter')
            ->with('formatter')
            ->willReturn($formatter);

        $expectedVo              = new SensorVO();
        $expectedVo->name        = $name;
        $expectedVo->type        = $sensorType2;
        $expectedVo->description = $description;
        $expectedVo->parameter   = null;
        $expectedVo->interval    = $interval;
        $expectedVo->formatter   = 'formatter';
        $expectedVo->node        = $node;
        $expectedVo->color       = '#b06893';

        $this->gateway
            ->expects($this->once())
            ->method('addSensor')
            ->with($expectedVo);

        $input = [
            '--force',
            'name'          => $name,
            'type'          => $sensorType2,
            'interval'      => $interval,
            'node'          => $node,
            'description'   => $description,
        ];
        $commandTester->execute($input);

        $display = $commandTester->getDisplay();
        $this->assertEquals(
            "Sensor value: 12°\n",
            $display
        );
    }

    public function testAddNoParametrized()
    {
        /** @var Sensor $sensor */
        $sensor = $this->getMock(Sensor::class);

        $sensorVo = new SensorVO();

        $this->subject->getParameter($sensorVo, $sensor);

        $this->assertNull($sensorVo->parameter);
    }

    public function testAddWithInput()
    {
        $parameter = 'parameter';
        /** @var TestSensorParameterized $sensor */
        $sensor    = $this->getMock(TestSensorParameterized::class);

        /** @var OutputInterface|MockObject $output */
        $output = $this->getMock(OutputInterface::class);
        /** @var InputInterface|MockObject $input */
        $input  = $this->getMock(InputInterface::class);

        $input
            ->expects($this->exactly(2))
            ->method('getArgument')
            ->with('parameter')
            ->willReturn($parameter);

        $this->subject->setInputOutput($input, $output);

        $sensorVo = new SensorVO();
        $this->subject->getParameter($sensorVo, $sensor);

        $this->assertEquals($parameter, $sensorVo->parameter);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage No possible sensor found
     */
    public function testAddWithoutSearch()
    {
        /** @var SearchableTestSensor|MockObject $sensor */
        $sensor    = $this->getMock(SearchableTestSensor::class);

        /** @var OutputInterface|MockObject $output */
        $output = $this->getMock(OutputInterface::class);
        /** @var InputInterface|MockObject $input */
        $input  = $this->getMock(InputInterface::class);

        $sensor
            ->expects($this->once())
            ->method('search')
            ->willReturn([]);

        $input
            ->expects($this->once())
            ->method('getArgument')
            ->with('parameter')
            ->willReturn(null);

        $this->subject->setInputOutput($input, $output);
        $sensorVo = new SensorVO();

        $this->subject->getParameter($sensorVo, $sensor);
    }
}
