<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;
use Homie\Sensors\Chart;
use Homie\Sensors\SensorGateway;

/**
 * @Widget
 */
class SensorGraph extends AbstractWidget
{

    const TYPE = 'sensor_graph';

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @Inject("@SensorGateway")
     * @param SensorGateway $gateway
     */
    public function __construct(SensorGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Sensor Graph'),
            gettext('Displays a Sensor Graph of given sensors'),
            $this->getParameters()
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 5);
    }

    /**
     * @return string[]
     */
    protected function getSenors()
    {
        $sensors = [];
        foreach ($this->gateway->getSensors() as $sensor) {
            $sensors[$sensor['sensorId']] = $sensor['name'];
        }
        return $sensors;
    }

    /**
     * @return array
     */
    protected function getParameters()
    {
        return [
            'sensor_ids' => [
                'type' => WidgetMetadataVo::MULTI_SELECT,
                'name' => gettext('Sensors'),
                'values' => $this->getSenors()
            ],
            'from' => [
                'type' => WidgetMetadataVo::SINGLE_SELECT,
                'name' => gettext('From'),
                'values' => Chart::getTimeSpans(),
                'default' => 86400
            ]
        ];
    }
}
