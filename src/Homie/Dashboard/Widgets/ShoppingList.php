<?php

namespace Homie\Dashboard\Widgets;

use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;

/**
 * @Widget
 */
class ShoppingList extends AbstractWidget
{

    const TYPE = 'shopping_list';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            ('Shopping List'),
            ('Displays/manage current shopping list'),
            []
        );
        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
