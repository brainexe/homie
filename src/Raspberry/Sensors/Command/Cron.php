<?php

namespace Raspberry\Sensors\Command;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Traits\TimeTrait;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValueEvent;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\Builder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("Command.Sensor.Cron")
 */
class Cron extends Command
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
     * @var Builder
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
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force sensor measure');
    }

    /**
     * @Inject({
     *  "@SensorGateway", "@SensorValuesGateway","@SensorBuilder",
     *  "@Sensor.VOBuilder", "@EventDispatcher", "%node.id%"
     * })
     * @param SensorGateway $gateway
     * @param SensorValuesGateway $valuesGateway
     * @param SensorBuilder $builder
     * @param Builder $voBuilder
     * @param EventDispatcher $dispatcher
     * @param integer $nodeId
     */
    public function __construct(
        SensorGateway $gateway,
        SensorValuesGateway $valuesGateway,
        SensorBuilder $builder,
        Builder $voBuilder,
        EventDispatcher $dispatcher,
        $nodeId
    ) {
        $this->builder         = $builder;
        $this->sensorGateway   = $gateway;
        $this->gateway         = $valuesGateway;
        $this->sensorVoBuilder = $voBuilder;
        $this->dispatcher      = $dispatcher;
        $this->nodeId          = $nodeId;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = $this->now();
        $sensors = $this->sensorGateway->getSensors($this->nodeId);

        foreach ($sensors as $sensorData) {
            $sensorVo = $this->sensorVoBuilder->buildFromArray($sensorData);
            $interval = $sensorVo->interval ?: 1;
            $lastRun  = $sensorVo->lastValueTimestamp;
            $delta    = $now - $lastRun;

            if ($delta > $interval * 60 || $input->getOption('force')) {
                $sensor = $this->builder->build($sensorVo->type);
                $currentSensorValue = $sensor->getValue($sensorVo->pin);
                if ($currentSensorValue === null) {
                    $output->writeln(sprintf(
                        '<error>Invalid sensor value: #%d %s (%s)</error>',
                        $sensorVo->sensorId,
                        $sensorVo->type,
                        $sensorVo->name
                    ));
                    continue;
                }

                $formattedSensorValue = $sensor->formatValue($currentSensorValue);
                $event = new SensorValueEvent(
                    $sensorVo,
                    $sensor,
                    $currentSensorValue,
                    $formattedSensorValue,
                    $now
                );
                $this->dispatcher->dispatchEvent($event);

                $this->gateway->addValue($sensorVo->sensorId, $currentSensorValue);

                $output->writeln(
                    sprintf(
                        '<info>Sensor value: #%d %s (%s): %s</info>',
                        $sensorVo->sensorId,
                        $sensorVo->type,
                        $sensorVo->name,
                        $formattedSensorValue
                    )
                );
            }
        }
    }
}
