<?php

namespace Homie\Dashboard\Widgets;


use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;
use Homie\Espeak\Espeak;
use Homie\Espeak\Speakers;

/**
 * @Widget
 */
class Speak extends AbstractWidget
{

    const TYPE = 'speak';

    /**
     * @var Speakers
     */
    private $speakers;

    /**
     * @param Speakers $speakers
     */
    public function __construct(Speakers $speakers)
    {
        $this->speakers = $speakers;
    }

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata() : WidgetMetadataVo
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            [
                'speaker' => [
                    'name'    => gettext('Speaker'),
                    'values'  => $this->speakers->getSpeakers(),
                    'type'    => WidgetMetadataVo::SINGLE_SELECT,
                    'default' => Espeak::DEFAULT_SPEAKER
                ]
            ]
        );
        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
