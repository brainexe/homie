<?php

namespace Raspberry\Console;

use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class CleanCronCommand extends Command
{

    /**
     * @var SensorValuesGateway
     */
    private $sensorValuesGateway;

    /**
     * @var SensorGateway
     */
    private $sensorGateway;

    /**
     * @var array
     */
    private $valueDeleteSensorValues;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:clean')
        ->setDescription('Delete old sensor values');
    }

    /**
     * @Inject({"@SensorValuesGateway", "@SensorGateway", "%delete_sensor_values%"})
     * @param SensorValuesGateway $valuesGateway
     * @param SensorGateway $gateway
     * @param integer[] $deleteValues
     */
    public function __construct(SensorValuesGateway $valuesGateway, SensorGateway $gateway, $deleteValues)
    {
        $this->sensorValuesGateway = $valuesGateway;
        $this->valueDeleteSensorValues = $deleteValues;
        $this->sensorGateway = $gateway;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sensor_ids = $this->sensorGateway->getSensorIds();

        foreach ($sensor_ids as $sensor_id) {
            $this->deleteOldValues($output, $sensor_id);
        }

        $output->writeln('<info>done</info>');
    }

    /**
     * @param OutputInterface $output
     * @param integer $sensor_id
     */
    private function deleteOldValues(OutputInterface $output, $sensor_id)
    {
        $deleted_rows = 0;

        foreach ($this->valueDeleteSensorValues as $delete) {
            $deleted_rows += $this->sensorValuesGateway->deleteOldValues(
                $sensor_id,
                $delete['days'],
                $delete['percentage']
            );
        }

        $output->writeln(sprintf('<info>sensor #%d, deleted %d rows</info>', $sensor_id, $deleted_rows));
    }
}
