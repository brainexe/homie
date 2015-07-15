<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Service;

use Homie\Dashboard\AbstractWidget;

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
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Egg Timer'),
            gettext('Simple egg timer'),
            [],
            4
        );

        return $metadata->setSize(4, 3);
    }
}
