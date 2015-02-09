<?php

namespace Raspberry\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use Raspberry\Dashboard\AbstractWidget;
use Raspberry\Sensors\SensorGateway;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class WggTimerWidget extends AbstractWidget
{

    const TYPE = 'egg_timer';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {

        return new WidgetMetadataVo(
            $this->getId(),
            _('Egg Timer'),
            []
        );
    }
}
