<?php

namespace Raspberry\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use Raspberry\Dashboard\AbstractWidget;
use Raspberry\Sensors\SensorGateway;

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
     * @param array $payload
     * @return mixed|void
     * @throws UserException
     */
    public function create(array $payload)
    {
        if (empty($payload['sensor_id'])) {
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
            $sensors[$sensor['id']] = $sensor['name'];
        }

        return new WidgetMetadataVo(
            $this->getId(),
            _('Sensor'),
            [
                'sensor_id' => [
                    'name' => _('Sensor ID'),
                    'values' => $sensors
                ]
            ]
        );
    }
}
