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
        $this->sensorValuesGateway     = $valuesGateway;
        $this->valueDeleteSensorValues = $deleteValues;
        $this->sensorGateway           = $gateway;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sensorIds = $this->sensorGateway->getSensorIds();

        foreach ($sensorIds as $sensorId) {
            $this->deleteOldValues($output, $sensorId);
        }

        $output->writeln('<info>done</info>');
    }

    /**
     * @param OutputInterface $output
     * @param integer $sensorId
     */
    private function deleteOldValues(OutputInterface $output, $sensorId)
    {
        $deletedRows = 0;

        foreach ($this->valueDeleteSensorValues as $delete) {
            $deletedRows += $this->sensorValuesGateway->deleteOldValues(
                $sensorId,
                $delete['days'],
                $delete['percentage']
            );
        }

        $output->writeln(sprintf('<info>sensor #%d, deleted %d rows</info>', $sensorId, $deletedRows));
    }
}
