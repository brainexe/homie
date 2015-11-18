<?php

namespace Homie\Dashboard\Widgets;

use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;

/**
 * @Widget
 */
class EggTimer extends AbstractWidget
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
            []
        );

        return $metadata->setSize(4, 3);
    }
}
