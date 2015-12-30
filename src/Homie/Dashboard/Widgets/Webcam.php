<?php

namespace Homie\Dashboard\Widgets;

use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;

/**
 * @Widget
 */
class Webcam extends AbstractWidget
{
    const TYPE = 'webcam';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            ('Webcam'),
            ('Take shots')
        );

        $metadata->parameters['showImage'] = [
            'name'   => gettext('Show recent image'),
            'type'   => WidgetMetadataVo::KEY_BOOLEAN
        ];

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
