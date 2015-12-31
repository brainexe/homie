<?php

namespace IntegrationTests;

use Homie\Expression\Language;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;

class ExpressionLanguageTest extends TestCase
{
    /**
     * @dataProvider provideExpressions
     * @param string $expression
     * @param string $expected
     * @param array $parameters
     */
    public function testEvaluate($expression, $expected, $parameters = [])
    {
        $dic = $this->bootstrap();

        /** @var Language $language */
        $language   = $dic->get('Expression.Language');

        $actual = $language->evaluate($expression, $parameters);

        $this->assertEquals($expected, $actual);
    }

    public function provideExpressions()
    {
        return [
            ['1 + 2', "3"],
            ['isEvent("foo") ? 1 : 0', "0"],
        ];
    }

    /**
     * @return Container
     */
    private function bootstrap()
    {
        /** @var Container $dic */
        global $dic;

        $dic->set('logger', new Logger('', [new TestHandler()]));
        @session_start();

        return $dic;
    }
}
