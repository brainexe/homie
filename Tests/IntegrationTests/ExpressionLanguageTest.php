<?php

namespace IntegrationTests;

use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use Homie\Expression\Entity;
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
        $language = $dic->get('Expression.Language');

        $actual = $language->evaluate($expression, $parameters);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provideExpressionsCompiler
     * @param string $expression
     * @param string $expected
     */
    public function testCompile($expression, $expected)
    {
        $dic = $this->bootstrap();

        /** @var Language $language */
        $language = $dic->get('Expression.Language');

        $actual = $language->compile($expression, $language->getParameterNames());

        $this->assertEquals($expected, $actual);
    }

    public function provideExpressions()
    {
        $timingEvent = new TimingEvent('timingId');

        $entity = new Entity();
        $entity->payload = [
            'foo' => 'bar'
        ];

        return [
            ['1 + 2', "3"],
            ['round(42.222)', "42"],
            ['isEvent("foo")', false, [
                'eventName' => 'other'
            ]],
            ['isEvent("foo")', true, [
                'eventName' => 'foo'
            ]],
            ['isTiming("timingId")', true, [
                'eventName' => TimingEvent::TIMING_EVENT,
                'event' => $timingEvent
            ]],
            ['isTiming("otherTimingId")', false, [
                'eventName' => TimingEvent::TIMING_EVENT,
                'event' => $timingEvent
            ]],
            ['isTiming("otherTimingId")', false, [
                'eventName' => 'otherEvent',
                'event' => $timingEvent
            ]],
            ['getProperty("foo")', 'bar', [
                'entity' => $entity
            ]],
            ['setProperty("foo", "bar2")', null, [
                'entity' => $entity
            ]],
            ['getProperty("foo", "bar2")', 'bar2', [
                'entity' => $entity
            ]],
        ];
    }

    public function provideExpressionsCompiler()
    {
        return [
            ['1 + 2', "(1 + 2)"],
            ['setProperty("foo", "bar")', '($entity->payload["foo"] = "bar")'],
            ['getProperty("foo")', '$entity->payload["foo"]'],
            ['round(eventName)', "round(\$eventName)"],
            ['isEvent("foo")', '($eventName == "foo")'],
            [
                'isTiming("timingId")',
                '($eventName == \'' . TimingEvent::TIMING_EVENT . '\' && $event->getTimingId() === "timingId")'
            ],
            [
                'voice("/Hallo (.*)/")',
                '($eventName == \'voice.text\' && preg_match("/Hallo (.*)/", $event->getText(), $entity->payload[\'voice\']))'
            ],
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
