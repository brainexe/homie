<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;
use Homie\Switches\Switches;

/**
 * @Widget
 */
class SwitchWidget extends AbstractWidget
{

    const TYPE = 'switch';

    /**
     * @var Switches
     */
    private $switches;

    /**
     * @Inject("@Switches.Switches")
     * @param Switches $switches
     */
    public function __construct(Switches $switches)
    {
        $this->switches = $switches;
    }

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $values = [];

        foreach ($this->switches->getAll() as $switchId => $switch) {
            $values[$switchId] = $switch->name;
        }

        $metadata = new WidgetMetadataVo(
            $this->getId(),
            ('Switch'),
            ('Control your switches.'),
            [
                'switchIds' => [
                    'name'   => gettext('Switch'),
                    'values' => $values,
                    'type'   => WidgetMetadataVo::MULTI_SELECT
                ]
            ]
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
