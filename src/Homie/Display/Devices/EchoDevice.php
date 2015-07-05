<?php

namespace Homie\Display\Devices;

class EchoDevice implements DeviceInterface
{

    /**
     * @param string $content
     */
    public function display($content)
    {
        echo $content;
    }
}
