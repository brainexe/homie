<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;
use Homie\Espeak\Espeak;

/**
 * @Widget
 */
class Speak extends AbstractWidget
{

    const TYPE = 'speak';

    /**
     * @var Espeak
     */
    private $espeak;

    /**
     * @Inject("@Espeak")
     * @param Espeak $espeak
     */
    public function __construct(Espeak $espeak)
    {
        $this->espeak = $espeak;
    }

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            ('Speak'),
            ('Speaks a given text.'),
            [
                'speaker' => [
                    'name'    => gettext('Speaker'),
                    'values'  => $this->espeak->getSpeakers(),
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
