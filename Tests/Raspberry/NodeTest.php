<?php

namespace Tests\Raspberry\Node;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Node;

/**
 * @Covers Raspberry\Node
 */
class NodeTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Node
	 */
	private $_subject;

	/**
	 * @var integer
	 */
	private $_node_id;

	public function setUp() {
		$this->_node_id = 5;

		$this->_subject = new Node($this->_node_id);
	}

	public function testGetNodeId() {
		$actual_result = $this->_subject->getNodeId();
		$this->assertEquals($this->_node_id, $actual_result);
	}

	public function testIsMaster() {
		$actual_result = $this->_subject->isMaster();
		$this->assertFalse($actual_result);
	}

	public function testIsSlave() {
		$actual_result = $this->_subject->isSlave();
		$this->assertTrue($actual_result);
	}

}
