<?php

namespace Homie\Dashboard\Widgets;

use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;

/**
 * @Widget
 */
class SpeechRecognition extends AbstractWidget
{

    const TYPE = 'speech_recognition';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata() : WidgetMetadataVo
    {
        $metadata = new WidgetMetadataVo(
            $this->getId()
        );

        return $metadata
            ->addTitle()
            ->setSize(3, 3);
    }
}
