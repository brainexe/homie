<?php

namespace Homie\Sensors\GetValue;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Traits\TimeTrait;
use Exception;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorValueEvent;
use Homie\Sensors\SensorValuesGateway;
use Homie\Sensors\SensorVO;

/**
 * @EventListener("Sensor.GetValue.Listener")
 */
class Listener
{

    use TimeTrait;
    use EventDispatcherTrait;
    use LoggerTrait;

    /**
     * @var SensorBuilder
     */
    private $builder;

    /**
     * @var SensorValuesGateway
     */
    private $gateway;

    /**
     * @Inject({
     *      "@SensorBuilder",
     *      "@SensorValuesGateway"
     * })
     * @param SensorBuilder $builder
     * @param SensorValuesGateway $valuesGateway
     */
    public function __construct(
        SensorBuilder $builder,
        SensorValuesGateway $valuesGateway
    ) {
        $this->builder = $builder;
        $this->gateway = $valuesGateway;
    }

    /**
     * @Listen(GetSensorValueEvent::NAME)
     * @param GetSensorValueEvent $event
     */
    public function handle(GetSensorValueEvent $event)
    {
        $sensorVo = $event->getSensorVO();

        $value = $this->getValue($sensorVo);
        if ($value === null) {
            $this->dispatcher->dispatchEvent(new SensorValueEvent(
                SensorValueEvent::ERROR,
                $sensorVo,
                $value,
                null,
                $this->now()
            ));
            return;
        }

        $this->gateway->addValue($sensorVo, $value);

        $this->dispatch($sensorVo, $value);
    }

    /**
     * @param SensorVO $sensorVo
     * @return float|null
     */
    private function getValue(SensorVO $sensorVo)
    {
        $sensor = $this->builder->build($sensorVo->type);

        try {
            return $sensor->getValue($sensorVo);
        } catch (Exception $e) {
            $this->error(
                sprintf(
                    'Error while fetching sensor %s: %s',
                    $sensorVo->name,
                    $e->getMessage()
                )
            );
            return null;
        }
    }

    /**
     * @param SensorVO $sensorVo
     * @param float|null $value
     */
    private function dispatch(SensorVO $sensorVo, $value)
    {
        $formatter      = $this->builder->getFormatter($sensorVo->formatter);
        $formattedValue = $formatter->formatValue($value);

        $event = new SensorValueEvent(
            SensorValueEvent::VALUE,
            $sensorVo,
            $value,
            $formattedValue,
            $this->now()
        );
        $this->dispatcher->dispatchEvent($event);

        $this->debug(
            sprintf('Sensor value for %s: %s', $sensorVo->name, $formattedValue),
            [
                'channel'  => 'sensor',
                'sensor'   => $sensorVo->name,
                'value'    => $value
            ]
        );
    }
}
