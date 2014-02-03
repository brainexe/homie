<?php

namespace Raspberry\Client;

use ArrayIterator;
use InvalidArgumentException;

/**
 * PinsCollection.
 *
 * @uses \IteratorAggregate
 * @uses \Countable
 *
 * @author Cédric Dugat <cedric@dugat.me>
 */
class PinsCollection implements \IteratorAggregate, \Countable {
	/**
	 * @var ArrayIterator
	 */
	private $coll;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->coll = new ArrayIterator();
	}

	/**
	 * Get iterator.
	 *
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return $this->coll;
	}

	/**
	 * @param Pin $pin Pin
	 */
	public function add(Pin $pin) {
		$this->coll[$pin->getID()] = $pin;
	}

	/**
	 * @param integer $id ID
	 * @return Pin
	 * @throws InvalidArgumentException
	 */
	public function get($id) {
		if (false === array_key_exists($id, $this->coll)) {
			throw new InvalidArgumentException(sprintf('Pin #%s does not exist', $id));
		}

		return $this->coll[$id];
	}

	/**
	 * Count.
	 *
	 * @return integer
	 */
	public function count() {
		return count($this->getIterator());
	}

	/**
	 * isEmpty.
	 *
	 * @return boolean
	 */
	public function isEmpty() {
		return (bool)$this->count();
	}

	/**
	 * Clear collection.
	 */
	public function clear() {
		$this->coll = new ArrayIterator();
	}
}
