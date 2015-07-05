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
            '/^radio (on|off) (\s+)$/i' => 'setRadio'
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
    public function setRadio(Event $event)
    {
        list ($status, $radioId) = $event->matches;

        $status = $status === 'on';

        $radioVo = $this->radios->getRadio($radioId);

        $event = new RadioChangeEvent($radioVo, $status);
        $this->dispatchEvent($event);
    }
}
