<?php

namespace Raspberry\Console;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Traits\TimeTrait;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValueEvent;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\SensorVOBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class SensorCronCommand extends Command
{

    use LoggerTrait;
    use TimeTrait;

    /**
     * @var SensorGateway
     */
    private $sensorGateway;

    /**
     * @var SensorValuesGateway
     */
    private $gateway;

    /**
     * @var SensorBuilder
     */
    private $builder;

    /**
     * @var integer
     */
    private $nodeId;

    /**
     * @var SensorVOBuilder
     */
    private $sensorVoBuilder;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:sensor')
        ->setDescription('Runs sensor cron')
        ->addOption('force', null, InputOption::VALUE_NONE, 'Force sensor mesasure');
    }

    /**
     * @Inject({"@SensorGateway", "@SensorValuesGateway","@SensorBuilder", "@SensorVOBuilder", "@EventDispatcher", "%node.id%"})
     * @param SensorGateway $gateway
     * @param SensorValuesGateway $valuesGateway
     * @param SensorBuilder $builder
     * @param SensorVOBuilder $voBuilder
     * @param EventDispatcher $dispatcher
     * @param integer $nodeId
     */
    public function __construct(
        SensorGateway $gateway,
        SensorValuesGateway $valuesGateway,
        SensorBuilder $builder,
        SensorVOBuilder $voBuilder,
        EventDispatcher $dispatcher,
        $nodeId
    ) {
        $this->builder = $builder;
        $this->sensorGateway = $gateway;
        $this->gateway = $valuesGateway;
        $this->sensorVoBuilder = $voBuilder;
        $this->dispatcher = $dispatcher;
        $this->nodeId = $nodeId;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = $this->now();
        $sensors = $this->sensorGateway->getSensors($this->nodeId);

        foreach ($sensors as $sensor_data) {
            $sensor_vo = $this->sensorVoBuilder->buildFromArray($sensor_data);

            $interval = $sensor_vo->interval ?: 1;

            $lastRunTimestamp = $sensor_vo->last_value_timestamp;
            $delta = $now - $lastRunTimestamp;
            if ($delta > $interval * 60 || $input->getOption('force')) {
                $sensor = $this->builder->build($sensor_vo->type);
                $currentSensorValue = $sensor->getValue($sensor_vo->pin);
                if ($currentSensorValue === null) {
                    $output->writeln(sprintf(
                        '<error>Invalid sensor value: #%d %s (%s)</error>',
                        $sensor_vo->id,
                        $sensor_vo->type,
                        $sensor_vo->name
                    ));
                    continue;
                }

                $formattedSensorValue = $sensor->formatValue($currentSensorValue);
                $event = new SensorValueEvent(
                    $sensor_vo,
                    $sensor,
                    $currentSensorValue,
                    $formattedSensorValue,
                    $now
                );
                $this->dispatcher->dispatchEvent($event);

                $this->gateway->addValue($sensor_vo->id, $currentSensorValue);

                $output->writeln(
                    sprintf(
                        '<info>Sensor value: #%d %s (%s): %s</info>',
                        $sensor_vo->id,
                        $sensor_vo->type,
                        $sensor_vo->name,
                        $formattedSensorValue
                    )
                );
            }
        }
    }
}
