<?php

namespace Homie\Dashboard\Widgets;



use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;
use Homie\Sensors\SensorGateway;

/**
 * @Widget
 */
class SensorInput extends AbstractWidget
{

    const TYPE = 'sensor_input';

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @param SensorGateway $gateway
     */
    public function __construct(SensorGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata() : WidgetMetadataVo
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            [
                'sensor_id' => [
                    'name'   => gettext('Sensor'),
                    'values' => $this->getSensors(),
                    'type'   => WidgetMetadataVo::SINGLE_SELECT
                ],
                'showSparkLine' => [
                    'name'   => gettext('Show spark line'),
                    'type'   => WidgetMetadataVo::KEY_BOOLEAN
                ]
            ]
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }

    /**
     * @return array
     */
    protected function getSensors()
    {
        $sensors = [];
        foreach ($this->gateway->getSensors() as $sensor) {
            $sensors[$sensor['sensorId']] = $sensor['name'];
        }

        return $sensors;
    }
}
