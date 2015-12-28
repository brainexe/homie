<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;
use Homie\Display\Gateway;

/**
 * @Widget
 */
class Display extends AbstractWidget
{

    const TYPE = 'display';

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject("@Display.Gateway")
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $displays = [];
        foreach ($this->gateway->getall() as $display) {
            $displays[$display->displayId] = $display->displayId;
        }

        $metadata = new WidgetMetadataVo(
            $this->getId(),
            ('Display'),
            ('Show rendered display content'),
            [
                'displayId' => [
                    'name'   => gettext('Display'),
                    'values' => $displays,
                    'type'   => 'single_select'
                ]
            ]
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
