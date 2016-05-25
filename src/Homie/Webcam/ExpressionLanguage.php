<?php

namespace Homie\Webcam;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use Generator;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Webcam.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;
    use IdGeneratorTrait;

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new Action('takePhoto', function () {
            $name  = $this->generateUniqueId('webcam');
            $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

            $this->dispatchInBackground($event);
        });

        yield new Action('takeVideo', function (array $parameters, int $duration) {
            unset($parameters);

            $name  = $this->generateUniqueId('webcam');
            $event = new WebcamEvent($name, WebcamEvent::TAKE_VIDEO, $duration);

            $this->dispatchInBackground($event);
        });
    }
}
