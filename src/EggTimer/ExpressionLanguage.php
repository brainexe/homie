<?php

namespace Homie\EggTimer;

use Exception;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @var EggTimer
     */
    private $eggTimer;

    /**
     * @param EggTimer $timer
     */
    public function __construct(EggTimer $timer)
    {
        $this->eggTimer = $timer;
    }

    /**
     * @return Generator|ExpressionFunction[]
     * @throws Exception
     */
    public function getFunctions()
    {
        yield new Action('eggTimer', function (array $variables, string $time, string $text = '') {
            unset($variables);
            $this->eggTimer->addNewJob($time, $text);
        });
    }
}
