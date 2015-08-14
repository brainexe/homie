<?php

namespace Homie\Dashboard\Widgets;

use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;

/**
 * @Widget
 */
class Status extends AbstractWidget
{
    const TYPE = 'status';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Status'),
            gettext('Show internal information')
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
