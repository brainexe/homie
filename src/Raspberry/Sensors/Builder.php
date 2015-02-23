<?php

namespace Raspberry\Sensors;

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
            $data['id'],
            $data['name'],
            $data['description'],
            $data['interval'],
            $data['node'],
            $data['pin'],
            $data['type'],
            $data['last_value'],
            $data['last_value_timestamp']
        );
    }

    /**
     * @param int $sensorId
     * @param string $name
     * @param string $description
     * @param int $interval
     * @param int $node
     * @param string $pin
     * @param string $type
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
        $pin,
        $type,
        $lastValue = null,
        $lastValueTimestamp = null
    ) {
        $sensor = new SensorVO();

        $sensor->sensorId           = $sensorId;
        $sensor->name               = $name;
        $sensor->description        = $description;
        $sensor->interval           = $interval;
        $sensor->node               = $node;
        $sensor->pin                = $pin;
        $sensor->type               = $type;
        $sensor->lastValue          = $lastValue;
        $sensor->lastValueTimestamp = $lastValueTimestamp;

        return $sensor;
    }
}
