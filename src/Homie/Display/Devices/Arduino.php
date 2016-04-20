<?php

namespace Homie\Display\Devices;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Arduino\SerialEvent;

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
     * @param string $content
     */
    public function display(string $content)
    {
        $pin = 1; // todo make configurable

        $event = new SerialEvent(SerialEvent::LCD, $pin, $content);

        $this->dispatcher->dispatchInBackground($event);
    }
}
