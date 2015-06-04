<?php

namespace Homie\Help;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;

/**
 * @ControllerAnnotation("HelpController")
 */
class Controller
{

    /**
     * @return string[]
     * @Route("/help/", name="help.all")
     * @Guest
     */
    public function get()
    {
        return [
            'index' => 'foobar'
        ];
    }
}
