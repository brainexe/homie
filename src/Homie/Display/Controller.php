<?php

namespace Homie\Display;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Display\Event\Redraw;

class Controller
{

    use EventDispatcherTrait;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @todo inject
     * @param Gateway $gateway
     * @param Renderer $renderer
     */
    public function __construct(Gateway $gateway, Renderer $renderer)
    {
        $this->gateway  = $gateway;
        $this->renderer = $renderer;
    }

    public function index()
    {
        $screens = $this->gateway->getall();

        return [
            'screens' => iterator_to_array($screens)
        ];
    }

    public function redraw()
    {
        $event = new Redraw();
        $this->dispatchInBackground($event);

        return true;
    }
}
