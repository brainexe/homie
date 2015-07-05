<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Dashboard\AbstractWidget;
use Homie\Espeak\Espeak;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class SpeakWidget extends AbstractWidget
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
            gettext('Speak'),
            gettext('Speaks a given text.'),
            [
                'speaker' => [
                    'name'    => gettext('Speaker'),
                    'values'  => $this->espeak->getSpeakers(),
                    'type'    => WidgetMetadataVo::SINGLE_SELECT,
                    'default' => Espeak::DEFAULT_SPEAKER
                ]
            ],
            4
        );
        $metadata->addTitle();

        return $metadata;
    }
}
