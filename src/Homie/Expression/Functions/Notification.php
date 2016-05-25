<?php

namespace Homie\Expression\Functions;

use BrainExe\Core\Notification\Notification as NotificationEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Homie\Expression\Action;
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
        yield new Action('addNotification', function (array $variables, string $message, string $subject, $level = Logger::ALERT) {
            unset($variables);
            $event = new NotificationEvent($message, $subject, $level);

            $this->dispatchEvent($event);
        });
    }
}
