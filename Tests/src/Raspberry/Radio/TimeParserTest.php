<?php

namespace Raspberry\Tests\Radio;

use PHPUnit_Framework_TestCase;
use Raspberry\Radio\Radios;
use Raspberry\Radio\TimeParser;

class TimeParserTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TimeParser
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new TimeParser();
	}

	/**
	 * @dataProvider providerTimes
	 */
	public function testParse($input_string, $expected_eta) {
		if (false === $expected_eta) {
			$this->setExpectedException('Matze\Core\Application\UserException');
		}

		$now = time();
		$actual_seconds = $this->_subject->parseString($input_string);

		$this->assertEquals($now + $expected_eta, $actual_seconds);
	}

	/**
	 * @return array[]
	 */
	public static function providerTimes() {
		return [
			[0, 0],
			[2, 2],
			[-1, false],
			["2", 2],
			['5s', 5],
			['10S', 10],
			['5t', false],
			['7m', 7*60],
			['now', 0]
		];
	}
} 
