<?php

namespace Homie\Dashboard\Widgets;

use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;

/**
 * @Widget
 */
class Time extends AbstractWidget
{

    const TYPE = 'time';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Time'),
            gettext('Displays the current time'),
            []
        );

        return $metadata->setSize(3, 3);
    }
}
