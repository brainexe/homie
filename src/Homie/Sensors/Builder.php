<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("Sensor.VOBuilder", public=false)
 */
class Builder
{

    /**
     * @param array $data
     * @return SensorVO
     */
    public function buildFromArray(array $data)
    {
        return $this->build(
            $data['sensorId'],
            $data['name'],
            $data['description'],
            $data['interval'],
            $data['node'],
            $data['parameter'],
            $data['type'],
            $data['color'],
            $data['formatter'],
            $data['lastValue'],
            $data['lastValueTimestamp']
        );
    }

    /**
     * @param int $sensorId
     * @param string $name
     * @param string $description
     * @param int $interval
     * @param int $node
     * @param string $parameter
     * @param string $type
     * @param string $color
     * @param string $formatter
     * @param float $lastValue
     * @param int $lastValueTimestamp
     * @return SensorVO
     */
    public function build(
        $sensorId,
        $name,
        $description,
        $interval,
        $node,
        $parameter,
        $type,
        $color,
        $formatter = null,
        $lastValue = null,
        $lastValueTimestamp = null
    ) {
        $sensor = new SensorVO();

        $sensor->sensorId           = (int)$sensorId;
        $sensor->name               = $name;
        $sensor->description        = $description;
        $sensor->interval           = (int)$interval;
        $sensor->node               = (int)$node;
        $sensor->parameter          = $parameter;
        $sensor->type               = $type;
        $sensor->color              = $color;
        $sensor->lastValue          = $lastValue;
        $sensor->lastValueTimestamp = (int)$lastValueTimestamp;
        $sensor->formatter          = $formatter;

        return $sensor;
    }
}
