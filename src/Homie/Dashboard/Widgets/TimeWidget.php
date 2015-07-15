<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Service;
use Homie\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class TimeWidget extends AbstractWidget
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
