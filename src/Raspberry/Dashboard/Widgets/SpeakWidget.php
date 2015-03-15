<?php

namespace Raspberry\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Service;
use Raspberry\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class SpeakWidget extends AbstractWidget
{

    const TYPE = 'speak';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        return new WidgetMetadataVo(
            $this->getId(),
            _('Speak')
        );
    }
}
