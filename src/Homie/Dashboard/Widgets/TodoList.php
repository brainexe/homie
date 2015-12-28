<?php

namespace Homie\Dashboard\Widgets;

use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;

/**
 * @Widget
 */
class TodoList extends AbstractWidget
{
    const TYPE = 'todo_list';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            ('Todo List'),
            ('Displays/manage current todo list'),
            []
        );
        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
