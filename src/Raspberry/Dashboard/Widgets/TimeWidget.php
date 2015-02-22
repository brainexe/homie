<?php

namespace Raspberry\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Service;
use Raspberry\Dashboard\AbstractWidget;

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
        return new WidgetMetadataVo(
            $this->getId(),
            _('Time')
        );
    }
}
