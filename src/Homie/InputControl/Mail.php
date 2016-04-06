<?php

namespace Homie\InputControl;

use BrainExe\Core\Mail\SendMailEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("InputControl.Mail")
 */
class Mail implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

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
