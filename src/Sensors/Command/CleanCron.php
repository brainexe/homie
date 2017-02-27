<?php

namespace Homie\Sensors\Command;

use Homie\Sensors\DeleteOldValues;
use Homie\Sensors\SensorGateway;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("Command.Sensors.CleanCron")
 */
class CleanCron extends Command
{

    /**
     * @var DeleteOldValues
     */
    private $deleteOldValues;

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cron:clean')
            ->setDescription('Delete old sensor values');
    }

    /**
     * @param DeleteOldValues $deleteOldValues
     * @param SensorGateway $gateway
     */
    public function __construct(
        DeleteOldValues $deleteOldValues,
        SensorGateway $gateway
    ) {
        $this->deleteOldValues = $deleteOldValues;
        $this->gateway         = $gateway;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sensorIds = $this->gateway->getSensorIds();

        foreach ($sensorIds as $sensorId) {
            $this->deleteOldValues($output, $sensorId);
        }

        $output->writeln('<info>done</info>');
    }

    /**
     * @param OutputInterface $output
     * @param int $sensorId
     */
    private function deleteOldValues(OutputInterface $output, int $sensorId)
    {
        $deletedRows = $this->deleteOldValues->deleteValues($sensorId);

        $output->writeln(
            sprintf(
                '<info>sensor #%d, deleted %d rows</info>',
                $sensorId,
                $deletedRows
            )
        );
    }
}
