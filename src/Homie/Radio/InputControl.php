<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;

/**
 * @InputControlAnnotation(name="radio")
 */
class InputControl implements InputControlInterface
{

    use EventDispatcherTrait;

    /**
     * @var Radios
     */
    private $radios;

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
     * @Inject("@Radios")
     * @param Radios $radios
     */
    public function __construct(Radios $radios)
    {
        $this->radios = $radios;
    }

    /**
     * @param Event $event
     */
    public function setSwitch(Event $event)
    {
        list ($status, $switchId) = $event->matches;

        $status = $status === 'on';

        $radioVo = $this->radios->get($switchId);

        $event = new SwitchChangeEvent($radioVo, $status);
        $this->dispatchEvent($event);
    }
}
