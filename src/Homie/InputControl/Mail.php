<?php

namespace Homie\InputControl;

use BrainExe\Core\Mail\SendMailEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;

/**
 * @InputControlAnnotation("InputControl.Mail")
 */
class Mail implements InputControlInterface
{

    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            '/^send mail "(.*)" "(.*)" "(.*)"$/i' => 'sendMail'
        ];
    }

    /**
     * @param Event $event
     */
    public function sendMail(Event $event)
    {
        list ($recipient, $subject, $body) = $event->matches;

        $event = new SendMailEvent($recipient, $subject, $body);

        $this->dispatchEvent($event);
    }
}
