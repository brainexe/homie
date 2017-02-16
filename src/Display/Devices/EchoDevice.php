<?php

namespace Homie\Display\Devices;

use Homie\Display\Annotation\DisplayDevice;
use Homie\Node;

/**
 * @DisplayDevice("Display.Devices.EchoDevice")
 */
class EchoDevice implements DeviceInterface
{

    const TYPE = 'echo';

    /**
     * @param Node $node
     * @param string $content
     */
    public function display(Node $node, string $content)
    {
        unset($node);

        echo $content;
    }

    /**
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE;
    }
}
