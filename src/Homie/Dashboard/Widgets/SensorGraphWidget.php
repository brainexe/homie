<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use Homie\Dashboard\AbstractWidget;
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
     * @param array $payload
     * @return mixed|void
     * @throws UserException
     */
    public function create(array $payload)
    {
        if (empty($payload['sensor_ids'])) {
            throw new UserException("No sensor_ids passed");
        }

        $validSensorIds = $this->gateway->getSensorIds();
        // todo check sensor id
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
                    'name'   => gettext('Sensor ID'),
                    'values' => $sensors
                ],
                'from' => [
                    'type'   => WidgetMetadataVo::SINGLE_SELECT,
                    'name'   => gettext('From'),
                    'values' => [
                        3600        => _('Last Hour'),
                        86400       => _('Last Day'),
                        86400 * 7   => _('Last Week'),
                        86400 * 30  => _('Last Month'),
                        -1          => _('All'),
                    ],
                    'default' => 86400
                ]
            ]
        );

        $metadata->addTitle();

        return $metadata;
    }
}
