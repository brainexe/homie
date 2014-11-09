<?php

namespace Tests\Raspberry\Controller\EspeakController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\EspeakController;
use Raspberry\Espeak\Espeak;
use BrainExe\Core\Util\TimeParser;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Controller\EspeakController
 */
class EspeakControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EspeakController
	 */
	private $_subject;

	/**
	 * @var Espeak|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEspeak;

	/**
	 * @var TimeParser|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTimeParser;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;


	public function setUp() {
		parent::setUp();

		$this->_mockEspeak = $this->getMock(Espeak::class, [], [], '', false);
		$this->_mockTimeParser = $this->getMock(TimeParser::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new EspeakController($this->_mockEspeak, $this->_mockTimeParser);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testIndex() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->index();
	}

	public function testSpeak() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->speak($request);
	}

	public function testDeleteJobJob() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->deleteJobJob($request);
	}

}
