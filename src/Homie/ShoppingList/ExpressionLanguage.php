<?php

namespace Homie\ShoppingList;

use BrainExe\Annotations\Annotations\Inject;
use Generator;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("ShoppingList.ExpressionLanguage", public=false)
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

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
    }
}
