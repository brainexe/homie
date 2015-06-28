<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Service;
use Homie\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class WebcamWidget extends AbstractWidget
{
    const TYPE = 'webcam';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        return new WidgetMetadataVo(
            $this->getId(),
            gettext('Webcam'),
            gettext('Take shots'),
            [],
            4
        );
    }
}
