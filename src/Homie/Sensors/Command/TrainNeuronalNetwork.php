<?php

namespace Homie\Sensors\Command;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Traits\TimeTrait;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;
use Symfony\Component\Console\Command\Command;
use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CommandAnnotation("Command.Sensors.TrainNeuronalNetwork")
 */
class TrainNeuronalNetwork extends Command
{
    use TimeTrait;

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @var SensorValuesGateway
     */
    private $valueGateway;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sensor:train');
    }

    /**
     * @Inject({
     *     "@SensorGateway",
     *     "@SensorValuesGateway"
     * })
     * @param SensorGateway $gateway
     * @param SensorValuesGateway $valueGateway
     */
    public function __construct(
        SensorGateway $gateway,
        SensorValuesGateway $valueGateway
    ) {
        parent::__construct();

        $this->gateway = $gateway;
        $this->valueGateway = $valueGateway;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $allSensors = $this->gateway->getSensors();
        $sensorIds = [];

        $row = ['time'];
        foreach ($allSensors as $sensor) {
            if ($sensor['interval'] > 0) {
                $sensorIds[] = $sensor['sensorId'];
                $row[] = $sensor['name'];
            }
        }

        $file = fopen(ROOT . 'cache/train.csv', 'w+');

        fputcsv($file, $row);

        $now = $this->now();
        for ($time = $now; $time >= $now - 86400 * 60; $time -= 60 * 10) {
            $values = iterator_to_array($this->valueGateway->getByTime($sensorIds, $time));
            $row = [$time];
            foreach ($values as $value) {
                $row[] = $value;
            }
            fputcsv($file, $row);
        }


        fclose($file);
    }
}
