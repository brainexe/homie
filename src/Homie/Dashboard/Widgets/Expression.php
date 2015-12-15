<?php

namespace Homie\Dashboard\Widgets;

use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;

/**
 * @Widget
 */
class Expression extends AbstractWidget
{
    const TYPE = 'expression';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Expression'),
            gettext('Evaluate any expression'),
            [
                'template' => [
                    'type'    => WidgetMetadataVo::TEXT_AREA,
                    'name'    => gettext('Template'),
                    'default' => '<b>{{value}}</b>!'
                ],
                'variables' => [
                    'type'    => WidgetMetadataVo::KEY_VALUE_LIST,
                    'name'    => gettext('Variables'),
                    'default' => array('value' => "sprintf('Hallo %s', 'Welt')")
                ],
                'reloadInterval' => [
                    'type'    => WidgetMetadataVo::NUMBER,
                    'name'    => gettext('Reload Interval in seconds'),
                    'min'     => -1,
                    'max'     => 3600,
                    'default' => -1
                ],
                'reloadButton' => [
                    'type'    => WidgetMetadataVo::KEY_BOOLEAN,
                    'name'    => gettext('Reload Button'),
                    'default' => true
                ],
            ]
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}
