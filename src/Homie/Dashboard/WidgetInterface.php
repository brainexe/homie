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

    /**
     * @param array $payload
     * @return mixed
     */
    public function create(array $payload);

    /**
     * @param array $payload
     * @return mixed
     */
    public function validate(array $payload);
}
