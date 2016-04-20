<?php

namespace Homie\Display\Devices;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("Display.Devices.EchoDevice", public=false)
 */
class EchoDevice implements DeviceInterface
{

    /**
     * @param string $content
     */
    public function display(string $content)
    {
        echo $content;
    }
}
