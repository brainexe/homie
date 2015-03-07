<?php

namespace Raspberry\Dashboard\Widgets;


use BrainExe\Annotations\Annotations\Service;

use Raspberry\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class EggTimerWidget extends AbstractWidget
{

    const TYPE = 'egg_timer';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {

        return new WidgetMetadataVo(
            $this->getId(),
            _('Egg Timer'),
            []
        );
    }
}
