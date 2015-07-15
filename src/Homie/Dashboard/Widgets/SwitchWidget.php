<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

use Homie\Dashboard\AbstractWidget;
use Homie\Radio\Radios;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class SwitchWidget extends AbstractWidget
{

    const TYPE = 'switch';

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

        foreach ($this->radios->getRadios() as $switchId => $radio) {
            $values[$switchId] = $radio->name;
        }

        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Switch'),
            gettext('Control your switches.'),
            [
                'switchIds' => [
                    'name'   => gettext('Switch'),
                    'values' => $values,
                    'type'   => WidgetMetadataVo::MULTI_SELECT
                ]
            ],
            4
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
