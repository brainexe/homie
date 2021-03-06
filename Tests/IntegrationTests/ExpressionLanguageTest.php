<?php

namespace IntegrationTests;

use BrainExe\Core\EventDispatcher\Events\TimingEvent;

use Homie\Expression\Language;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class ExpressionLanguageTest extends TestCase
{
    /**
     * @dataProvider provideExpressions
     * @param string $expression
     * @param string $expected
     * @param array $parameters
     */
    public function testEvaluate(string $expression, string $expected, array $parameters = [])
    {
        $dic = $this->bootstrap();

        /** @var Language $language */
        $language = $dic->get(Language::class);

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
        $language = $dic->get(Language::class);

        $actual = $language->compile($expression, $language->getParameterNames());

        $this->assertEquals($expected, $actual);
    }

    public function provideExpressions()
    {
        $timingEvent = new TimingEvent('timingId');

        return [
            ['1 + 2', '3'],
            ['round(42.222)', '42'],
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
            ]]
        ];
    }

    public function provideExpressionsCompiler()
    {
        return [
            ['1 + 2', "(1 + 2)"],
            ['round(eventName)', "round(\$eventName)"],
            ['isEvent("foo")', '($eventName === "foo")'],
            [
                'isTiming("timingId")',
                '($eventName === \'' . TimingEvent::TIMING_EVENT . '\' && $event->getTimingId() === "timingId")'
            ],
            [
                'voice("/Hallo (.*)/")',
                '($eventName === \'voice.text\' && preg_match("/Hallo (.*)/", $event->getText(), Homie\VoiceControl\ExpressionLanguage::$currentMatch))'
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
