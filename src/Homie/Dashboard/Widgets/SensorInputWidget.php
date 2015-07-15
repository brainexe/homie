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
class SensorInputWidget extends AbstractWidget
{

    const TYPE = 'sensor_input';

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

        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Input sensor value'),
            gettext('Displays the current value of a given sensor'),
            [
                'sensor_id' => [
                    'name'   => gettext('Sensor'),
                    'values' => $sensors,
                    'type'   => 'single_select'
                ]
            ]
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
