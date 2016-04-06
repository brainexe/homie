<?php

namespace Homie\Webcam;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;

/**
 * @ExpressionLanguageAnnotation("Webcam.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('takePhoto', function () {
            throw new InvalidArgumentException('Function takePhoto() not available as condition');
        }, function () {
            unset($variables);
            $name  = microtime(true);
            $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

            $this->dispatchInBackground($event);
        });
    }
}
