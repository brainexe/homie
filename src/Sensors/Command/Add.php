<?php

namespace Homie\Sensors\Command;

use Exception;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Interfaces\Searchable;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorVO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation
 */
class Add extends Command
{

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @var SensorBuilder
     */
    private $builder;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var QuestionHelper
     */
    private $helper;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sensor:add')
            ->setDescription('Add a new Sensor')
            ->addArgument('type', InputArgument::OPTIONAL)
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addArgument('parameter', InputArgument::OPTIONAL)
            ->addArgument('description', InputArgument::OPTIONAL)
            ->addArgument('node', InputArgument::OPTIONAL)
            ->addArgument('interval', InputArgument::OPTIONAL);
    }

    /**
     * @param SensorGateway $gateway
     * @param SensorBuilder $builder
     */
    public function __construct(SensorGateway $gateway, SensorBuilder $builder)
    {
        $this->gateway = $gateway;
        $this->builder = $builder;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
        $this->helper = $this->getHelperSet()->get('question');

        $sensorVo = new SensorVO();

        $sensor      = $this->getSensor();
        $this->getParameter($sensorVo, $sensor);
        $name        = $this->getSensorName($sensor);
        $description = $this->getSensorDescription();
        $interval    = $this->getInterval();
        $node        = $this->getNode();
        $type        = $sensor->getSensorType();
        $formatter   = $sensor->getDefinition()->formatter;

        $sensorVo->node = $node;

        // get test value
        try {
            $testValue = $sensor->getValue($sensorVo);
            $formatterModel = $this->builder->getFormatter($formatter);
            $output->writeln(
                sprintf('<info>Sensor value: %s</info>', $formatterModel->formatValue($testValue))
            );
        } catch (InvalidSensorValueException $e) {
            $output->writeln(sprintf('<error>Sensor returned invalid data: %s</error>', $e->getMessage()));
            $this->askForTermination();
        }

        $sensorVo->name        = $name;
        $sensorVo->type        = $type;
        $sensorVo->description = $description;
        $sensorVo->interval    = $interval;
        $sensorVo->formatter   = $formatter;
        $sensorVo->color       = '#' . substr(md5($name), 0, 6);

        $this->gateway->addSensor($sensorVo);
    }

    /**
     * @throws Exception
     */
    private function askForTermination()
    {
        $question = new ConfirmationQuestion('Abort adding this sensor? (y/n)');
        if ($this->helper->ask($this->input, $this->output, $question)) {
            throw new Exception('Terminated');
        }
    }

    /**
     * @param SensorVO $sensorVo
     * @param Sensor $sensor
     * @throws Exception
     */
    public function getParameter(SensorVO $sensorVo, Sensor $sensor)
    {
        if (!$sensor instanceof Parameterized) {
            return;
        }

        if ($this->input->getArgument('parameter')) {
            $sensorVo->parameter = $this->input->getArgument('parameter');
            return;
        }

        $sensorVo->parameter = $this->getRawParameter($sensor);

        /** @var Sensor $sensor */
        if (!$sensor->isSupported($sensorVo, $this->output)) {
            $this->output->writeln('<error>Sensor is not supported</error>');
            throw new Exception(sprintf('Parameter "%s" is not supported', $sensorVo->parameter));
        }

        $this->output->writeln('<info>Sensor is supported</info>');
    }

    /**
     * @return Sensor
     */
    protected function getSensor()
    {
        $sensors = $this->builder->getSensors();

        if ($this->input->getArgument('type')) {
            $type = $this->input->getArgument('type');
        } else {
            $sensorTypes = array_keys($sensors);

            $question = new ChoiceQuestion('Sensor Type', $sensorTypes);
            $type     = $this->helper->ask($this->input, $this->output, $question);
        }

        return $sensors[$type];
    }

    /**
     * @param Sensor $sensor
     * @return string
     */
    protected function getSensorName(Sensor $sensor)
    {
        if ($this->input->getArgument('name')) {
            return $this->input->getArgument('name');
        }

        $default = ucfirst($sensor->getSensorType());

        $name = $this->helper->ask(
            $this->input,
            $this->output,
            new Question(sprintf('Sensor name? (default: %s)' . PHP_EOL, $default), $sensor->getSensorType())
        );

        return $name;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function setInputOutput(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
    }

    /**
     * @return int
     */
    protected function getInterval()
    {
        if ($this->input->getArgument('interval')) {
            return (int)$this->input->getArgument('interval');
        }

        return (int)$this->helper->ask(
            $this->input,
            $this->output,
            new Question('Interval in minutes (default: 5)' . PHP_EOL, 5)
        );
    }

    /**
     * @return int
     */
    protected function getNode()
    {
        if ($this->input->getArgument('node')) {
            return (int)$this->input->getArgument('node');
        }

        return (int)$this->helper->ask(
            $this->input,
            $this->output,
            new Question('Node? (only for advanced users needed)' . PHP_EOL)
        );
    }

    /**
     * @return string
     */
    protected function getSensorDescription()
    {
        if ($this->input->getArgument('description')) {
            return $this->input->getArgument('description');
        }

        $description = $this->helper->ask(
            $this->input,
            $this->output,
            new Question('Description (optional)?' . PHP_EOL)
        );

        return $description;
    }

    /**
     * @param Sensor $sensor
     * @return string
     * @throws Exception
     */
    private function getRawParameter(Sensor $sensor)
    {
        if ($sensor instanceof Searchable) {
            $possible = $sensor->search();
            if (!$possible) {
                throw new Exception('No possible sensor found');
            }
            $question = new ChoiceQuestion('Parameter', $possible);
        } else {
            $question = new Question('Parameter?' . PHP_EOL);
        }

        return $this->helper->ask($this->input, $this->output, $question);
    }
}
