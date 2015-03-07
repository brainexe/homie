<?php

namespace Raspberry\Sensors\Command;

use BrainExe\Annotations\Annotations\Inject;
use Exception;
use Raspberry\Sensors\Interfaces\Searchable;
use Raspberry\Sensors\Interfaces\Sensor;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorVO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("Command.Sensor.Add")
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
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sensor:add')
            ->setDescription('Add a new Sensor');
    }

    /**
     * @Inject({"@SensorGateway", "@SensorBuilder"})
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
        /** @var QuestionHelper $helper */
        $helper = $this->getHelperSet()->get('question');

        $sensor = $this->getSensor($input, $output, $helper);

        $parameter   = $this->getParameter($input, $output, $sensor, $helper);
        $name        = $helper->ask($input, $output, new Question("Sensor name?\n"));
        $description = $helper->ask($input, $output, new Question("Description (optional)?\n"));

        $interval    = (int)$helper->ask($input, $output, new Question("Interval in minutes\n")) ?: 1;
        $node        = (int)$helper->ask($input, $output, new Question("Node\n"));

        // get test value
        $testValue = $sensor->getValue($parameter);
        if ($testValue !== null) {
            $output->writeln(
                sprintf('<info>Sensor value: %s</info>', $sensor->formatValue($testValue))
            );
        } else {
            $output->writeln('<error>Sensor returned invalid data.</error>');
            $this->askForTermination($helper, $input, $output);
        }

        $sensorVo              = new SensorVO();
        $sensorVo->name        = $name;
        $sensorVo->type        = $sensor->getSensorType();
        $sensorVo->description = $description;
        $sensorVo->pin         = $parameter;
        $sensorVo->interval    = $interval;
        $sensorVo->node        = $node;

        $this->gateway->addSensor($sensorVo);
    }

    /**
     * @param QuestionHelper $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    private function askForTermination(
        QuestionHelper $helper,
        InputInterface $input,
        OutputInterface $output
    ) {
        $question = new ConfirmationQuestion('Abort adding this sensor? (y/n)');
        if ($helper->ask($input, $output, $question)) {
            throw new Exception('Terminated');
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Sensor $sensor
     * @param QuestionHelper $helper
     * @return string
     * @throws Exception
     */
    protected function getParameter(
        InputInterface $input,
        OutputInterface $output,
        Sensor $sensor,
        QuestionHelper $helper
    ) {
        $parameter = null;
        if ($sensor instanceof Searchable) {
            $possible = $sensor->search();
            if ($possible) {
                $question  = new ChoiceQuestion("Parameter", $possible);
                $parameter = $helper->ask($input, $output, $question);
            } else {
                throw new Exception('No possible sensor found');
            }
        } else {
            $parameter = $helper->ask($input, $output, new Question("Parameter (Optional)?\n"));
        }

        if (!$sensor->isSupported($parameter, $output)) {
            $output->writeln('<error>Sensor is not supported</error>');
            throw new Exception(sprintf('Parameter "%s" is not supported', $parameter));
        } else {
            $output->writeln('<info>Sensor is supported</info>');
        }

        return $parameter;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $helper
     * @return Sensor
     */
    protected function getSensor(InputInterface $input, OutputInterface $output, QuestionHelper $helper)
    {
        $sensors     = $this->builder->getSensors();
        $sensorTypes = array_keys($sensors);

        $question = new ChoiceQuestion("Sensor Type", $sensorTypes);
        $type     = $helper->ask($input, $output, $question);
        $sensor   = $sensors[$type];

        return $sensor;
    }
}
