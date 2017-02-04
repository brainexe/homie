<?php

namespace Homie\Dashboard\Widgets;


use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;
use Homie\Sensors\Chart;
use Homie\Sensors\SensorGateway;

/**
 * @Widget
 */
class SensorWidget extends AbstractWidget
{

    const TYPE = 'sensor';

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
                'from' => [
                    'type'   => WidgetMetadataVo::SINGLE_SELECT,
                    'name'   => gettext('From'),
                    'values' => Chart::getTimeSpans(),
                    'default' => 86400
                ]
            ]
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }

    /**
     * @return string[]
     */
    private function getSensors()
    {
        $sensors = [];
        foreach ($this->gateway->getSensors() as $sensor) {
            $sensors[$sensor['sensorId']] = $sensor['name'];
        }

        return $sensors;
    }
}
