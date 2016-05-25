<?php

namespace Homie\Expression\Functions;

use BrainExe\Core\Mail\SendMailEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Homie\Expression\Action;
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
        yield new Action('sendMail', function (array $variables, string $recipient, string $subject, string $body) {
            unset($variables);
            $event = new SendMailEvent($recipient, $subject, $body);

            $this->dispatchInBackground($event);
        });
    }
}
