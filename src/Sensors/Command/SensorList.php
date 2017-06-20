<?php

namespace Homie\Sensors\Command;

use Homie\Sensors\SensorGateway;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation
 */
class SensorList extends Command
{
    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @param SensorGateway $gateway
     */
    public function __construct(
        SensorGateway $gateway
    ) {
        parent::__construct();

        $this->gateway = $gateway;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sensor:list')
            ->setDescription('Dumps a list of all available servers')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders([
            'id',
            'name',
            'value',
            'type',
            'interval',
        ]);

        foreach ($this->gateway->getSensorIds() as $sensorId) {
            $sensor = $this->gateway->getSensor($sensorId);

            $table->addRow([
                $sensor['sensorId'],
                $sensor['name'],
                $sensor['lastValue'],
                $sensor['type'],
                $sensor['interval'],
            ]);
        }

        $table->render();
    }
}
