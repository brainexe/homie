<?php

namespace Tests\Homie\Expression;

use Homie\Expression\Language;
use PHPUnit_Framework_TestCase as TestCase;

class LanguageTest extends TestCase
{

    /**
     * @var Language
     */
    private $subject;

    public function setup()
    {
        $this->subject = new Language();
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

    public function testGetFunctionNames()
    {
        $actual = $this->subject->getFunctionNames();

        $this->assertInternalType('array', $actual);
    }

    public function testGetParameterNames()
    {
        $actual = $this->subject->getParameterNames();

        $this->assertInternalType('array', $actual);
    }
}
