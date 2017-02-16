<?php

namespace Homie\Display\Devices;

use BrainExe\Annotations\Annotations\Service;
use InvalidArgumentException;

/**
 * @Service("Display.Devices.Factory", public=true)
 */
class Factory
{
    /**
     * @var DeviceInterface[]
     */
    private $devices;

    /**
     * @param DeviceInterface[] $devices
     */
    public function __construct(array $devices)
    {
        $this->devices = $devices;
    }

    /**
     * @return DeviceInterface[]
     */
    public function getAll() : array
    {
        return $this->devices;
    }

    /**
     * @param string $type
     * @return DeviceInterface
     */
    public function getDevice(string $type) : DeviceInterface
    {
        if (empty($this->devices[$type])) {
            throw new InvalidArgumentException(sprintf('Invalid device %s', $type));
        }

        return $this->devices[$type];
    }
}
