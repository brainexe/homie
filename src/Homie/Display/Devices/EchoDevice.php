<?php

namespace Homie\Display\Devices;

use BrainExe\Annotations\Annotations\Service;
use Homie\Node;

/**
 * @Service("Display.Devices.EchoDevice", public=false)
 */
class EchoDevice implements DeviceInterface
{

    /**
     * @param Node $node
     * @param string $content
     */
    public function display(Node $node, string $content)
    {
        unset($node);

        echo $content;
    }
}
