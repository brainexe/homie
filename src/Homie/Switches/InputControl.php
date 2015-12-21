<?php

namespace Homie\Switches;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;

/**
 * @InputControlAnnotation(name="switch")
 */
class InputControl implements InputControlInterface
{

    use EventDispatcherTrait;

    /**
     * @var Switches
     */
    private $switches;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            '/^radio (on|off) (\s+)$/i' => 'setSwitch',
            '/^switch (on|off) (\s+)$/i' => 'setSwitch',
        ];
    }

    /**
     * @Inject("@Switch.Switches")
     * @param Switches $switches
     */
    public function __construct(Switches $switches)
    {
        $this->switches = $switches;
    }

    /**
     * @param Event $event
     */
    public function setSwitch(Event $event)
    {
        list ($status, $switchId) = $event->matches;

        $status = $status === 'on';

        $switch = $this->switches->get($switchId);

        $event = new SwitchChangeEvent($switch, $status);
        $this->dispatchEvent($event);
    }
}
