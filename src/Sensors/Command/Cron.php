<?php

namespace Homie\Sensors\Command;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Traits\TimeTrait;
use Homie\Sensors\GetValue\GetSensorValueEvent;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValueEvent;
use Homie\Sensors\Builder;
use Homie\Sensors\SensorVO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation
 */
class Cron extends Command
{
    use TimeTrait;

    /**
     * @var SensorGateway
     */
    private $sensorGateway;

    /**
     * @var Builder
     */
    private $sensorVoBuilder;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var OutputInterface
     */
    private $output;

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
     *  "nodeId" = "%node.id%"
     * })
     * @param SensorGateway $gateway
     * @param Builder $voBuilder
     * @param EventDispatcher $dispatcher
     */
    public function __construct(
        SensorGateway $gateway,
        Builder $voBuilder,
        EventDispatcher $dispatcher
    ) {
        $this->sensorGateway   = $gateway;
        $this->sensorVoBuilder = $voBuilder;
        $this->dispatcher      = $dispatcher;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $now     = $this->now();
        $sensors = $this->sensorGateway->getSensors();

        $this->dispatcher->addListener(SensorValueEvent::VALUE, [$this, 'handleEvent']);
        $this->dispatcher->addListener(SensorValueEvent::ERROR, [$this, 'handleErrorEvent']);

        foreach ($sensors as $sensorData) {
            $this->handleSensor($input, $sensorData, $now);
        }
    }

    /**
     * @param SensorValueEvent $event
     */
    public function handleEvent(SensorValueEvent $event)
    {
        $this->output->writeln(
            sprintf(
                '#%d: <info>%s</info> (<info>%s</info>): <info>%s</info>',
                $event->sensorVo->sensorId,
                $event->sensorVo->type,
                $event->sensorVo->name,
                $event->valueFormatted
            )
        );
    }
    /**
     * @param SensorValueEvent $event
     */
    public function handleErrorEvent(SensorValueEvent $event)
    {
        $this->output->writeln(
            sprintf(
                '#%d: <error>Error while fetching value of sensor %s</error> (<info>%s</info>)</info>',
                $event->sensorVo->sensorId,
                $event->sensorVo->type,
                $event->sensorVo->name
            )
        );
    }

    /**
     * @param SensorVO $sensorVo
     */
    protected function getValue($sensorVo)
    {
        $event = new GetSensorValueEvent($sensorVo);
        $this->dispatcher->dispatchEvent($event);
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param InputInterface $input
     * @param array $sensorData
     * @param int $now
     */
    private function handleSensor(InputInterface $input, $sensorData, $now)
    {
        $sensorVo = $this->sensorVoBuilder->buildFromArray($sensorData);
        $interval = $this->getInterval($sensorVo);

        if ($interval < 0) {
            return;
        }

        $lastRun = $sensorVo->lastValueTimestamp;
        $delta   = $now - $lastRun;
        if ($delta > $interval * 60 || $input->getOption('force')) {
            $this->getValue($sensorVo);
        }
    }

    /**
     * @param SensorVO $sensorVo
     * @return int
     */
    private function getInterval($sensorVo) : int
    {
        return $sensorVo->interval ?: 1;
    }
}
