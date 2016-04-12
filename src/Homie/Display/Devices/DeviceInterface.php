<?php

namespace Homie\Display\Devices;

interface DeviceInterface
{
    /**
     * @param string $content
     */
    public function display(string $content);
}
