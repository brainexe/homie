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
            // todo check sensor id
            throw new UserException("No sensor_id passed");
        }
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

        return new WidgetMetadataVo(
            $this->getId(),
            gettext('Sensor Graph'),
            [
                'sensor_ids' => [
                    'type'   => 'array',
                    'name'   => gettext('Sensor ID'),
                    'values' => $sensors
                ]
            ]
        );
    }
}