<?php

namespace Homie\Dashboard;

use Homie\Dashboard\Widgets\WidgetMetadataVo;

interface WidgetInterface
{

    /**
     * @return string
     */
    public function getId();

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata();
}
