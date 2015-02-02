<?php

namespace Raspberry\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use Raspberry\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class SensorWidget extends AbstractWidget
{

    const TYPE = 'sensor';

    /**
     * @return string
     */
    public function getId()
    {
        return self::TYPE;
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
        return new WidgetMetadataVo(
            $this->getId(),
            _('Sensor'),
            [
            'sensor_id' => _('Sensor ID')
            ]
        );
    }
}
