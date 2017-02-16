<?php

namespace Homie\Expression\Controller;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Homie\Expression\Variable;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Expression.Controller.Variables")
 */
class Variables
{

    /**
     * @var Variable
     */
    private $variable;

    /**
     * @param Variable $variable
     */
    public function __construct(
        Variable $variable
    ) {
        $this->variable = $variable;
    }

    /**
     * @return array
     * @Route("/expressions/variables/", name="expressions.variables", methods={"GET"})
     */
    public function getAll() : array
    {
        return $this->variable->getAll();
    }

    /**
     * @param Request $request
     * @param string $key
     * @param string $value
     * @return bool
     * @Route("/expressions/variable/{key}/{value}/", name="expressions.variable.set", methods={"POST"})
     */
    public function setVariable(Request $request, string $key, string $value) : bool
    {
        unset($request);

        $this->variable->setVariable($key, $value);

        return true;
    }

    /**
     * @param Request $request
     * @param string $key
     * @return bool
     * @Route("/expressions/variable/{key}/", name="expressions.variable.delete", methods={"DELETE"})
     */
    public function deleteVariable(Request $request, string $key) : bool
    {
        unset($request);

        $this->variable->deleteVariable($key);

        return true;
    }
}
