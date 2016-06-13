<?php

namespace Tests\Homie\Expression;

use Homie\Expression\Language;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
}
