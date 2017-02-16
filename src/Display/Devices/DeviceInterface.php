<?php

namespace Homie\Display\Devices;

use Homie\Node;

interface DeviceInterface
{
    /**
     * @param Node $node
     * @param string $content
     */
    public function display(Node $node, string $content);

    /**
     * @return string
     */
    public static function getType() : string;
}
