<?php

namespace Homie\Display\Devices;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Arduino\SerialEvent;
use Homie\Display\Annotation\DisplayDevice;
use Homie\Node;

/**
 * @DisplayDevice("Display.Devices.Arduino")
 */
class Arduino implements DeviceInterface
{
    const TYPE = 'ardiono';

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Node $node
     * @param string $content
     */
    public function display(Node $node, string $content)
    {
        $pin = (int)$node->getOption('displayPin');

        $event = new SerialEvent(SerialEvent::LCD, $pin, $content);

        $this->dispatcher->dispatchInBackground($event);
    }

    /**
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE;
    }
}
