<?php

namespace Raspberry;

/**
 * @Service(public=false)
 */
class Node {

	/**
	 * @var integer
	 */
	private $_node_id;

	/**
	 * @Value("%node.id%")
	 */
	public function __construct($node_id) {
		$this->_node_id = $node_id;
	}

	/**
	 * @return integer
	 */
	public function getNodeId() {
		return $this->_node_id;
	}

	/**
	 * @return boolean
	 */
	public function isMaster() {
		return $this->_node_id == 0;
	}

	/**
	 * @return boolean
	 */
	public function isSlave() {
		return $this->_node_id > 0;
	}
}