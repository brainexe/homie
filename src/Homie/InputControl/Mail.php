<?php

namespace Homie\InputControl;

use BrainExe\Core\Mail\SendMailEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;
use Generator;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @InputControlAnnotation("InputControl.Mail", tags={{"name"="expression_language"}}, public=false)
 */
class Mail implements InputControlInterface, ExpressionFunctionProviderInterface
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

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('sendMail', function ($recipient, $subject, $body) {
            unset($recipient, $subject, $body);
            throw new InvalidArgumentException('Function sendMail() not available as condition');
        }, function (array $variables, $recipient, $subject, $body) {
            unset($variables);
            $event = new SendMailEvent($recipient, $subject, $body);

            $this->dispatchInBackground($event);
        });
    }
}
