<?php

namespace Homie\Sensors\Command;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\Time;
use Homie\Sensors\Builder;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValueEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation
 */
class SensorValue extends Command
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var Time
     */
    private $time;

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @param EventDispatcher $dispatcher
     * @param Time $time
     * @param SensorGateway $gateway
     * @param Builder $builder
     */
    public function __construct(
        EventDispatcher $dispatcher,
        Time $time,
        SensorGateway $gateway,
        Builder $builder
    ) {
        parent::__construct();

        $this->dispatcher = $dispatcher;
        $this->time = $time;
        $this->gateway = $gateway;
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sensor:value')
            ->setDescription('Adds a value for a list of sensors')
            ->addArgument('values', InputArgument::REQUIRED | InputArgument::IS_ARRAY)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = $this->time->now();

        foreach ($input->getArgument('values') as $raw) {
            list ($sensorId, $value) = explode(':', $raw, 2);

            $sensor = $this->gateway->getSensor($sensorId);
            if (empty($sensor)) {
                $output->writeln(sprintf(
                    '<error>Invalid sensor-id: %d</error>',
                    $sensorId
                ));
                continue;
            }
            $sensor = $this->builder->buildFromArray($sensor);

            $event = new SensorValueEvent(
                SensorValueEvent::VALUE_RAW,
                $sensor,
                $value,
                $value,
                $now
            );
            $this->dispatcher->dispatchEvent($event);
        }
    }
}
