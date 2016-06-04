<?php

namespace Homie\ShoppingList;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("ShoppingList.ExpressionLanguage", public=false)
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{
    use EventDispatcherTrait;

    /**
     * @var ShoppingList
     */
    private $list;

    /**
     * @Inject({
     *     "@ShoppingList"
     * })
     * @param ShoppingList $list
     */
    public function __construct(ShoppingList $list)
    {
        $this->list = $list;
    }

    /**
     * @return Generator
     */
    public function getFunctions()
    {
        yield new Action('addShoppingListItem', function (array $parameters, string $name) {
            unset($parameters);
            $this->list->addItem($name);
        });

        yield new Action('removeShoppingListItem', function (array $parameters, string $name) {
            unset($parameters);
            $this->list->removeItem($name);
        });

        yield new Action('sayShoppingList', function () {
            $text = implode('. ', $this->list->getItems());
            $espeakVo = new EspeakVO($text, null, null, null, EspeakVO::BROWSER_ONLY);

            $event = new EspeakEvent($espeakVo);
            $this->dispatchEvent($event);
        });
    }
}
