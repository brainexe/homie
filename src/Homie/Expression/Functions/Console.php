<?php

namespace Homie\Expression\Functions;

use BrainExe\Core\EventDispatcher\Events\ConsoleEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Expression.Functions.Console")
 */
class Console implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new Action('console', function (array $variables, string $command) {
            unset($variables);
            $event = new ConsoleEvent($command);

            $this->dispatchInBackground($event);
        });
    }
}
