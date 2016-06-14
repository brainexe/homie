<?php

namespace Homie\Display\Devices;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Arduino\SerialEvent;
use Homie\Node;

/**
 * @Service("Display.Devices.Arduino", public=false)
 */
class Arduino implements DeviceInterface
{

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @Inject("@EventDispatcher")
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
}
