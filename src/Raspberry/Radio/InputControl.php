<?php

namespace Raspberry\Radio;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @InputControl(name="radio")
 */
class InputControl implements EventSubscriberInterface
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
        '/^radio (on|off) (\d)$/' => 'setRadio'
        ];
    }

    /**
     * @inject("@Radios")
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
