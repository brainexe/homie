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
    public function getMetadata() : WidgetMetadataVo
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            [
                'switchIds' => [
                    'name'   => gettext('Switch'),
                    'values' => $this->getSwitches(),
                    'type'   => WidgetMetadataVo::MULTI_SELECT
                ]
            ]
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }

    /**
     * @return array
     */
    protected function getSwitches()
    {
        $values = [];
        foreach ($this->switches->getAll() as $switchId => $switch) {
            $values[$switchId] = $switch->name;
        }
        return $values;
    }
}
