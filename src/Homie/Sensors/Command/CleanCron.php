<?php

namespace Homie\Sensors\Command;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;
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
     * @var SensorValuesGateway
     */
    private $valuesGateway;

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
     * @Inject({"@SensorValuesGateway", "@SensorGateway"})
     * @param SensorValuesGateway $valuesGateway
     * @param SensorGateway $gateway
     */
    public function __construct(
        SensorValuesGateway $valuesGateway,
        SensorGateway $gateway
    ) {
        $this->valuesGateway      = $valuesGateway;
        $this->gateway            = $gateway;

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
     * @param integer $sensorId
     */
    private function deleteOldValues(OutputInterface $output, $sensorId)
    {
        $deletedRows = $this->valuesGateway->deleteOldValues($sensorId);

        $output->writeln(
            sprintf(
                '<info>sensor #%d, deleted %d rows</info>',
                $sensorId,
                $deletedRows
            )
        );
    }
}
