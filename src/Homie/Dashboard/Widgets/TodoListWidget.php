<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Service;
use Homie\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class TodoListWidget extends AbstractWidget
{

    const TYPE = 'todo_list';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Todo List'),
            gettext('Displays/manage current todo list'),
            []
        );
        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
