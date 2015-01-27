<?php

namespace Raspberry\Console;

use Exception;
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

/**
 * @Command
 */
class SensorAddCommand extends Command
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

        $sensors     = $this->builder->getSensors();
        $sensorTypes = array_keys($sensors);

        $question = new ChoiceQuestion("Sensor Type", $sensorTypes);
        $type     = $helper->ask($input, $output, $question);
        $sensor   = $sensors[$type];

        if (!$sensor->isSupported($output)) {
            $output->writeln('<error>Sensor is not supported</error>');
            $this->askForTermination($helper, $input, $output);
        } else {
            $output->writeln('<info>Sensor is supported</info>');
        }

        $name        = $helper->ask($input, $output, new Question("Sensor name?\n"));
        $description = $helper->ask($input, $output, new Question("Description (optional)?\n"));
        $pin         = $helper->ask($input, $output, new Question("Pin (Optional)?\n"));
        $interval    = (int)$helper->ask($input, $output, new Question("Interval in minutes\n")) ?: 1;
        $node        = (int)$helper->ask($input, $output, new Question("Node\n"));

        // get test value
        $testValue = $sensor->getValue($pin);
        if ($testValue !== null) {
            $output->writeln(sprintf('<info>Sensor value: %s</info>', $sensor->formatValue($testValue)));
        } else {
            $output->writeln('<error>Sensor returned invalid data.</error>');
            $this->askForTermination($helper, $input, $output);
        }

        $sensorVo              = new SensorVO();
        $sensorVo->name        = $name;
        $sensorVo->type        = $type;
        $sensorVo->description = $description;
        $sensorVo->pin         = $pin;
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
    private function askForTermination(QuestionHelper $helper, InputInterface $input, OutputInterface $output)
    {
        $question = new ConfirmationQuestion('Abort adding this sensor? (y/n)');
        if ($helper->ask($input, $output, $question)) {
            throw new Exception('Terminated');
        }
    }
}
