<?php

namespace Raspberry;

/**
 * @Service(public=false)
 */
class Node {

	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @Value("%node.id%")
	 * @param $node_id
	 */
	public function __construct($node_id) {
		$this->id = $node_id;
	}

	/**
	 * @return integer
	 */
	public function getNodeId() {
		return $this->id;
	}

	/**
	 * @return boolean
	 */
	public function isMaster() {
		return $this->id == 0;
	}

	/**
	 * @return boolean
	 */
	public function isSlave() {
		return $this->id > 0;
	}
}
