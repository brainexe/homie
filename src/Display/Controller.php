<?php

namespace Homie\Display;

use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\HttpFoundation\Request;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;

/**
 * @ControllerAnnotation(requirements={"displayId": "\d+"})
 */
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
     * @param Gateway $gateway
     * @param Renderer $renderer
     */
    public function __construct(Gateway $gateway, Renderer $renderer)
    {
        $this->gateway  = $gateway;
        $this->renderer = $renderer;
    }

    /**
     * @Route("/display/", name="display.index", methods="GET")
     * @return array
     */
    public function index() : array
    {
        $screens = $this->gateway->getAll();

        return [
            'screens' => iterator_to_array($screens)
        ];
    }

    /**
     * @Route("/display/", name="display.add", methods="POST")
     * @param Request $request
     * @return Settings
     */
    public function add(Request $request) : Settings
    {
        $settings = $this->getSettingsFromRequest($request);

        $this->gateway->addDisplay($settings);

        return $settings;
    }

    /**
     * @Route("/display/{displayId}/", name="display.delete", methods="DELETE")
     * @param Request $request
     * @param int $displayId
     * @return bool
     */
    public function delete(Request $request, int $displayId) : bool
    {
        unset($request);

        $this->gateway->delete($displayId);

        return true;
    }

    /**
     * @Route("/display/{displayId}/", name="display.update", methods="PUT")
     * @param Request $request
     * @param int $displayId
     * @return Settings
     */
    public function update(Request $request, int $displayId) : Settings
    {
        $settings = $this->getSettingsFromRequest($request);
        $settings->displayId = $displayId;

        $this->gateway->update($settings);

        return $settings;
    }

    /**
     * @Route("/display/{displayId}/redraw/", name="display.redraw", methods="POST")
     * @param Request $request
     * @param int $displayId
     * @return Settings
     */
    public function redraw(Request $request, int $displayId) : Settings
    {
        unset($request);

        $display = $this->gateway->get($displayId);

        $display->content = $this->renderer->render($display);
        $this->gateway->update($display);

        return $display;
    }

    /**
     * @param Request $request
     * @return Settings
     */
    private function getSettingsFromRequest(Request $request) : Settings
    {
        $settings           = new Settings();
        $settings->lines    = $request->request->getInt('lines');
        $settings->columns  = $request->request->getInt('columns');
        $settings->content  = (array)$request->request->get('content');
        $settings->nodeId   = $request->request->getInt('nodeId');

        $settings->rendered = $this->renderer->render($settings);

        return $settings;
    }
}
