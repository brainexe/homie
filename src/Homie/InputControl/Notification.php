<?php

namespace Homie\InputControl;

use BrainExe\Core\Notification\Notification as NotificationEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use InvalidArgumentException;
use Monolog\Logger;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;

/**
 * @ExpressionLanguageAnnotation("InputControl.Notification")
 */
class Notification implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('addNotification', function ($message, $subject, $level = Logger::ALERT) {
            throw new InvalidArgumentException('Function addNotification() not available as condition');
        }, function (array $variables, $message, $subject, $level = Logger::ALERT) {
            unset($variables);
            $event = new NotificationEvent($message, $subject, $level);

            $this->dispatchEvent($event);
        });
    }
}
