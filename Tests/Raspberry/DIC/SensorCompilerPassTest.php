<?php

namespace Tests\Raspberry\DIC\SensorCompilerPass;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\DIC\SensorCompilerPass;

/**
 * @Covers Raspberry\DIC\SensorCompilerPass
 */
class SensorCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorCompilerPass
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new SensorCompilerPass();
	}

	public function testProcess() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->process($container);
	}

}
