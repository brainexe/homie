<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use Homie\Dashboard\AbstractWidget;
use Homie\Sensors\Chart;
use Homie\Sensors\SensorGateway;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class SensorGraphWidget extends AbstractWidget
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
        $sensors = [];
        foreach ($this->gateway->getSensors() as $sensor) {
            $sensors[$sensor['sensorId']] = $sensor['name'];
        }

        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Sensor Graph'),
            gettext('Displays a Sensor Graph of given sensors'),
            [
                'sensor_ids' => [
                    'type'   => WidgetMetadataVo::MULTI_SELECT,
                    'name'   => gettext('Sensors'),
                    'values' => $sensors
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
            ->setSize(4, 5);
    }
}
