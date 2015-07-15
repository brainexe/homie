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
class SensorWidget extends AbstractWidget
{

    const TYPE = 'sensor';

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
            gettext('Sensor'),
            gettext('Displays the current value of a given sensor'),
            [
                'sensor_id' => [
                    'name'   => gettext('Sensor'),
                    'values' => $sensors,
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
}
