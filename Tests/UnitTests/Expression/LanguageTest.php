<?php

namespace Tests\Homie\Expression;

use Generator;
use Homie\Expression\Action;
use Homie\Expression\Language;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class LanguageTest extends TestCase
{

    /**
     * @var Language
     */
    private $subject;

    public function setup()
    {
        /** @var ContainerInterface */
        global $dic;

        $this->subject = new Language($dic);
    }

    public function testTime()
    {
        $actual = $this->subject->evaluate('time()', []);

        $this->assertEquals(time(), $actual);
    }

    public function testTimeString()
    {
        $actual = $this->subject->compile('time()', []);

        $this->assertEquals('time()', $actual);
    }

    public function testEvaluateEmpty()
    {
        $actual = $this->subject->evaluate('');

        $this->assertEquals('', $actual);
    }

    public function testGetParameterNames()
    {
        $actual = $this->subject->getParameterNames();

        $this->assertInternalType('array', $actual);
    }

    public function testGetFunctions()
    {
        $actual = $this->subject->getFunctions();

        $this->assertInternalType('array', $actual);
    }

    public function testLazyLoad()
    {
        /** @var ContainerInterface $dic */
        global $dic;

        $serviceId    = 'testservice';
        $functionName = 'myFunction';

        $testExpression = new class implements ExpressionFunctionProviderInterface {
            /**
             * @return ExpressionFunction[]|Generator An array of Function instances
             */
            public function getFunctions()
            {
                yield new Action('myFunction', function (array $params, $a, $b, $c) {
                    unset($params);
                    return "$a/$b/$c";
                });
            }
        };
        $this->subject->lazyRegister($functionName, [$testExpression, 'getFunctions']);

        $dic->set($serviceId, $testExpression);

        $actual = $this->subject->evaluate('myFunction(1, 2, 3)');

        $this->assertEquals('1/2/3', $actual);
    }
}
