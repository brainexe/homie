<?php

namespace Homie\Espeak\Controller;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Homie\Espeak\Speakers as SpeakersModel;
use Iterator;

/**
 * @ControllerAnnotation
 */
class Speakers
{

    /**
     * @var SpeakersModel
     */
    private $speakers;

    /**
     * @param SpeakersModel $speakers
     */
    public function __construct(SpeakersModel $speakers)
    {
        $this->speakers = $speakers;
    }

    /**
     * @return Iterator
     * @Route("/espeak/speakers/", name="espeak.speakers", options={"cache":86400})
     */
    public function speakers() : Iterator
    {
        return $this->speakers->getSpeakers();
    }
}
