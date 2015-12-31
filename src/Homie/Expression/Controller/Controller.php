<?php

namespace Homie\Expression\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use Homie\Expression\Entity;
use Homie\Expression\Gateway;
use Homie\Expression\Language;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Expression.Controller")
 */
class Controller
{
    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @var Language
     */
    private $language;

    /**
     * @Inject({
     *  "@Expression.Gateway",
     *  "@Expression.Language"
     * })
     * @param Gateway $gateway
     * @param Language $language
     */
    public function __construct(
        Gateway  $gateway,
        Language $language
    ) {
        $this->gateway  = $gateway;
        $this->language = $language;
    }

    /**
     * @return Entity[]
     * @Route("/expressions/", name="expressions.load", methods="GET")
     */
    public function load()
    {
        return [
            'events'        => include ROOT . '/cache/events.php',
            'expressions'   => $this->gateway->getAll(),
            'functions'     => $this->language->getFunctionNames()
        ];
    }

    /**
     * @Route("/expressions/", name="expressions.save", methods="PUT")
     * @param Request $request
     * @return Entity
     * @throws UserException
     */
    public function save(Request $request)
    {
        $expressionId = $request->request->get('expressionId');

        $expressions = $this->gateway->getAll();
        if (isset($expressions[$expressionId])) {
            $entity = $expressions[$expressionId];
        } else {
            $entity = new Entity();
            $entity->expressionId = $expressionId;
        }

        $entity->conditions = (array)$request->request->get('conditions');
        $entity->actions    = (array)$request->request->get('actions');
        $entity->enabled    = (bool)$request->request->get('enabled');

        if (empty($entity->actions)) {
            throw new UserException(_('No actions defined'));
        }

        foreach ($entity->actions as $action) {
            $this->validate($action);
        }
        foreach ($entity->conditions as $condition) {
            $this->validate($condition);
        }

        $this->gateway->save($entity);

        return $entity;
    }

    /**
     * @param Request $request
     * @param $expressionId
     * @return bool
     * @Route("/expressions/{expressionId}/", name="expressions.delete", methods="DELETE")
     */
    public function delete(Request $request, $expressionId)
    {
        unset($request);

        return $this->gateway->delete($expressionId);
    }

    /**
     * @param Request $request
     * @Route("/expressions/evaluate/", name="expressions.evaluate", methods="GET")
     * @return string
     */
    public function evaluate(Request $request)
    {
        $expression = $request->query->get('expression');

        return $this->language->evaluate($expression, array());
    }


    /**
     * @param string $expression
     */
    private function validate($expression)
    {
        $this->language->parse($expression, $this->language->getParameterNames());
    }
}
