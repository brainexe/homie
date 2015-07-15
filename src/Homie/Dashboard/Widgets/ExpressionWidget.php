<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Service;
use Homie\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class ExpressionWidget extends AbstractWidget
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
