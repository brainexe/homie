<?php

namespace Homie\Expression\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Traits\FileCacheTrait;
use Homie\Expression\Entity;
use Homie\Expression\Gateway;
use Homie\Expression\Language;
use Homie\Expression\Listener\WriteFunctionCache;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Expression.Controller")
 */
class Controller
{
    use FileCacheTrait;

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
            'expressions' => $this->gateway->getAll(),
        ];
    }

    /**
     * @return array[]
     * @Route("/expressions/functions/", name="expressions.functions", methods="GET")
     */
    public function functions()
    {
        return $this->includeFile(WriteFunctionCache::CACHE);
    }

    /**
     * @return array[]
     * @Route("/expressions/events/", name="expressions.events", methods="GET")
     */
    public function events()
    {
        return $this->includeFile('events');
    }

    /**
     * @Route("/expressions/", name="expressions.save", methods="PUT")
     * @param Request $request
     * @return Entity
     * @throws UserException
     */
    public function save(Request $request) : Entity
    {
        $expressionId = $request->request->get('expressionId');
        if (empty($expressionId)) {
            throw new UserException(_('No expression id defined'));
        }

        $entity = $this->getEntity($expressionId);
        $entity->conditions = (array)$request->request->get('conditions');
        $entity->actions    = (array)$request->request->get('actions');
        $entity->enabled    = (bool)$request->request->get('enabled');

        $this->saveEntity($entity);

        return $entity;
    }

    /**
     * @param Request $request
     * @param string $expressionId
     * @return bool
     * @Route("/expressions/{expressionId}/", name="expressions.delete", methods="DELETE")
     */
    public function delete(Request $request, string $expressionId) : bool
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
    private function validate(string $expression)
    {
        $this->language->parse($expression, $this->language->getParameterNames());
    }

    /**
     * @param string $expressionId
     * @return Entity
     */
    private function getEntity(string $expressionId) : Entity
    {
        $expressions = $this->gateway->getAll();
        if (isset($expressions[$expressionId])) {
            return $expressions[$expressionId];
        } else {
            $entity               = new Entity();
            $entity->expressionId = $expressionId;
            return $entity;
        }
    }

    /**
     * @param Entity $entity
     * @throws UserException
     */
    private function saveEntity(Entity $entity)
    {
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
    }
}
