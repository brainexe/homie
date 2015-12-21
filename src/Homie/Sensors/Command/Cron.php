<?php

namespace Homie\Sensors\Command;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\EventDispatcher\EventDispatcher;

use BrainExe\Core\Traits\TimeTrait;
use Homie\Sensors\GetValue\Event;
use Homie\Sensors\SensorBuilder;
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
 * @CommandAnnotation("Command.Sensor.Cron")
 */
class Cron extends Command
{
    use TimeTrait;

    /**
     * @var SensorGateway
     */
    private $sensorGateway;

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
     *  "@SensorGateway",
     *  "@SensorBuilder",
     *  "@Sensor.VOBuilder",
     *  "@EventDispatcher",
     *  "%node.id%"
     * })
     * @param SensorGateway $gateway
     * @param SensorBuilder $builder
     * @param Builder $voBuilder
     * @param EventDispatcher $dispatcher
     * @param integer $nodeId
     */
    public function __construct(
        SensorGateway $gateway,
        SensorBuilder $builder,
        Builder $voBuilder,
        EventDispatcher $dispatcher,
        $nodeId
    ) {
        $this->builder         = $builder;
        $this->sensorGateway   = $gateway;
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
        $this->output = $output;

        $now     = $this->now();
        $sensors = $this->sensorGateway->getSensors();

        $this->dispatcher->addListener(SensorValueEvent::VALUE, [$this, 'handleEvent']);
        $this->dispatcher->addListener(SensorValueEvent::ERROR, [$this, 'handleErrorEvent']);

        foreach ($sensors as $sensorData) {
            $sensorVo = $this->sensorVoBuilder->buildFromArray($sensorData);
            $interval = $sensorVo->interval ?: 1;

            if ($interval < 0) {
                continue;
            }

            $lastRun = $sensorVo->lastValueTimestamp;
            $delta   = $now - $lastRun;

            if ($delta > $interval * 60 || $input->getOption('force')) {
                $this->getValue($sensorVo);
            }
        }
    }

    /**
     * @param SensorValueEvent $event
     */
    public function handleEvent(SensorValueEvent $event)
    {
        $definition = $this->builder->getDefinition($event->sensorVo->type);

        $this->output->writeln(
            sprintf(
                '#%d: <info>%s</info> (<info>%s</info>): <info>%s</info>',
                $event->sensorVo->sensorId,
                $definition->name,
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
        $definition = $this->builder->getDefinition($event->sensorVo->type);

        $this->output->writeln(
            sprintf(
                '#%d: <error>Error while fetching value of sensor %s</error> (<info>%s</info>)</info>',
                $event->sensorVo->sensorId,
                $definition->name,
                $event->sensorVo->name
            )
        );
    }

    /**
     * @param SensorVO $sensorVo
     */
    protected function getValue($sensorVo)
    {
        $event = new Event($sensorVo);
        $this->dispatcher->dispatchEvent($event);
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }
}
