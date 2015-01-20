<?php

namespace Raspberry\Console;

use Exception;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorVO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');

        $sensors     = $this->builder->getSensors();
        $sensorTypes = array_keys($sensors);

        $sensorTypeIdx = $dialog->select($output, "Sensor type?\n", $sensorTypes);
        $type   = $sensorTypes[$sensorTypeIdx];
        $sensor = $sensors[$type];

        if (!$sensor->isSupported($output)) {
            $output->writeln('<error>Sensor is not supported</error>');
            $this->askForTermination($dialog, $output);
        } else {
            $output->writeln('<info>Sensor is supported</info>');
        }

        $name        = $dialog->ask($output, "Sensor name\n");
        $description = $dialog->ask($output, "Description (optional)\n");
        $pin         = $dialog->ask($output, "Pin (optional)\n");
        $interval    = (int)$dialog->ask($output, "Interval in minutes\n") ?: 1;
        $node        = (int)$dialog->ask($output, "Node\n");

        // get test value
        $testValue = $sensor->getValue($pin);
        if ($testValue !== null) {
            $output->writeln(sprintf('<info>Sensor value: %s</info>', $sensor->formatValue($testValue)));
        } else {
            $output->writeln('<error>Sensor returned invalid data.</error>');
            $this->askForTermination($dialog, $output);
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
     * @param DialogHelper $dialog
     * @param OutputInterface $output
     * @throws Exception
     * @todo using deprecated Class
     */
    private function askForTermination(DialogHelper $dialog, OutputInterface $output)
    {
        if ($dialog->askConfirmation($output, 'Abort adding this sensor? (y/n)')) {
            throw new Exception('Terminated');
        }
    }
}
