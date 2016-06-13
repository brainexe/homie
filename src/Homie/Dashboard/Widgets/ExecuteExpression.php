<?php

namespace Homie\Dashboard\Widgets;

use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;

/**
 * @Widget
 */
class ExecuteExpression extends AbstractWidget
{
    const TYPE = 'execute_expression';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata() : WidgetMetadataVo
    {
        $metadata = new WidgetMetadataVo(
            $this->getId()
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
