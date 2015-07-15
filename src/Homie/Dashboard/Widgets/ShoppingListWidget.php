<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Service;
use Homie\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class ShoppingListWidget extends AbstractWidget
{

    const TYPE = 'shopping_list';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Shopping List'),
            gettext('Displays/manage current shopping list'),
            []
        );
        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
