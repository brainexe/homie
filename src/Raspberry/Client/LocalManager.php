<?php

namespace Raspberry\Client;

use Sly\RPIManager\Client\ClientInterface;
use Sly\RPIManager\IO\GPIO\Collection\PinsCollection;
use Sly\RPIManager\IO\GPIO\Manager;

class LocalManager extends Manager {
	const GPIO_COMMAND_READALL   = 'gpio readall';
	const GPIO_COMMAND_DIRECTION = 'gpio mode %d %s';
	const GPIO_COMMAND_VALUE     = 'gpio write %d %d';

	/**
	 * @var \Sly\RPIManager\Client\ClientInterface
	 */
	private $client;

	/**
	 * @var \Sly\RPIManager\IO\GPIO\Collection\PinsCollection
	 */
	private $pins;

	/**
	 * Constructor.
	 *
	 * @param \Sly\RPIManager\Client\ClientInterface $client Client
	 */
	public function __construct(ClientInterface $client)
	{
		$this->client = $client;
		$this->pins   = new PinsCollection();

		$this->init();
	}

	/**
	 * Init.
	 */
	private function init() {
		$results = $this->client->execute(self::GPIO_COMMAND_READALL);
		$results = explode("\n", $results);
		$results = array_slice($results, 3, -2);

		foreach ($results as $r) {
			$matches = explode('|', $r);
			$matches = array_map('trim', $matches);

			$pin = new Pin();
			$pin->setID($matches[1]);
			$pin->setName($matches[4]);
			$pin->setDirection($matches[5]);
			$pin->setValue((int)('High' == $matches[6]));

			$this->pins->add($pin);
		}
	}

	/**
	 * Get pins (and their statuses).
	 *
	 * @return \Sly\RPIManager\IO\GPIO\Collection\PinsCollection
	 */
	public function getPins() {
		return $this->pins;
	}

	/**
	 * Update.
	 *
	 * @param Pin $pin Pin
	 *
	 * @return Pin
	 */
	public function update(Pin $pin) {
		$pinValue = Pin::VALUE_HIGH == $pin->getValue() ? 1 : 0;

		$this->client->execute(sprintf(self::GPIO_COMMAND_DIRECTION, $pin->getID(), $pin->getDirection()));
		$this->client->execute(sprintf(self::GPIO_COMMAND_VALUE, $pin->getID(), $pinValue));

		return $pin;
	}
} 