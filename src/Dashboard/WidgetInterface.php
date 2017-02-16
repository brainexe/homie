<?php

namespace Homie\Dashboard;

use Homie\Dashboard\Widgets\WidgetMetadataVo;

interface WidgetInterface
{
    const TYPE = '';

    /**
     * @return string
     */
    public function getId() : string;

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata() : WidgetMetadataVo;
}
