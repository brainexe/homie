<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

use Homie\Dashboard\AbstractWidget;
use Homie\Radio\Radios;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class RadioWidget extends AbstractWidget
{

    const TYPE = 'radio';

    /**
     * @var Radios
     */
    private $radios;

    /**
     * @Inject("@Radios")
     * @param Radios $radios
     */
    public function __construct(Radios $radios)
    {
        $this->radios = $radios;
    }

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $values = [];

        foreach ($this->radios->getRadios() as $radioId => $radio) {
            $values[$radioId] = $radio->name;
        }

        return new WidgetMetadataVo(
            $this->getId(),
            gettext('Radio'),
            gettext('Control your radio switches.'),
            [
                'radioId' => [
                    'name'   => gettext('Radio ID'),
                    'values' => $values,
                    'type'   => 'single_select'
                ]
            ],
            4
        );
    }
}
