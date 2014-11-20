<?php

namespace Raspberry\Gpio;

use Raspberry\Client\ClientInterface;

/**
 * @service(public=false)
 */
class PinLoader {

	/**
	 * @var ClientInterface
	 */
	private $_local_client;

	/**
	 * @var PinsCollection
	 */
	private $_pins = null;

	/**
	 * @Inject("@RaspberryClient")
	 * @param ClientInterface $local_client
	 */
	public function __construct(ClientInterface $local_client) {
		$this->_local_client = $local_client;
	}

	/**
	 * @param string $pin
	 * @return Pin
	 */
	public function loadPin($pin) {
		$pins = $this->loadPins();

		return $pins->get($pin);
	}

	/**
	 * @return PinsCollection
	 */
	public function loadPins(){
		if (null !== $this->_pins) {
			return $this->_pins;
		}

		$results = $this->_local_client->executeWithReturn(GpioManager::GPIO_COMMAND_READALL);
		$results = explode("\n", $results);
		$results = array_slice($results, 3, -2);

		$this->_pins = new PinsCollection();
		foreach ($results as $r) {
			$matches = explode('|', $r);
			$matches = array_map('trim', $matches);

			$pin = new Pin();
			$pin->setID((int)$matches[1]);
			$pin->setName($matches[4]);
			$pin->setDirection($matches[5]);
			$pin->setValue((int)('High' === $matches[6]));

			$this->_pins->add($pin);
		}

		return $this->_pins;
	}

}