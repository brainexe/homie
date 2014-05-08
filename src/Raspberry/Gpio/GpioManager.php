<?php

namespace Raspberry\Gpio;

use Raspberry\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class GpioManager {

	const GPIO_COMMAND_READALL = 'gpio readall';
	const GPIO_COMMAND_DIRECTION = 'gpio mode %d %s';
	const GPIO_COMMAND_VALUE = 'gpio write %d %d';

	/**
	 * @var ClientInterface
	 */
	private $_local_client;

	/**
	 * @var PinGateway
	 */
	private $_pin_gateway;

	/**
	 * @var PinsCollection
	 */
	private $_pins = null;

	/**
	 * @Inject({"@PinGateway", "@RaspberryClient"})
	 */
	public function __construct(PinGateway $pin_gateway, ClientInterface $local_client) {
		$this->_pin_gateway = $pin_gateway;
		$this->_local_client = $local_client;
	}

	/**
	 * @return PinsCollection
	 */
	public function getPins() {
		$descriptions = $this->_pin_gateway->getPinDescriptions();
		try {
			$this->_loadPins();
		} catch (\RuntimeException $e) {
			$this->_pins = new PinsCollection();

			$pin = new Pin();
			$pin->setID(2);
			$pin->setName('GPIO 2');
			$pin->setDirection('OUT');
			$pin->setValue(true);
			$this->_pins->add($pin);

			$pin = new Pin();
			$pin->setName('GPIO 3');
			$pin->setID(3);
			$pin->setDirection('IN');
			$pin->setValue(false);
			$this->_pins->add($pin);
		}

		foreach ($this->_pins as $pin) {
			/** @var Pin $pin */
			if (!empty($descriptions[$pin->getId()])) {
				$pin->setDescription($descriptions[$pin->getId()]);
			}
		}

		return $this->_pins;
	}

	/**
	 * @param integer $id
	 * @param string $status
	 * @param boolean $value
	 */
	public function setPin($id, $status, $value) {
		$this->_loadPins();

		$pin = $this->_pins->get($id);

		$pin->setDirection($status ? 'out' : 'in');
		$pin->setValue($value ? Pin::VALUE_HIGH : Pin::VALUE_LOW);

		$this->_updatePin($pin);
	}

	/**
	 * @param Pin $pin Pin
	 */
	private function _updatePin(Pin $pin) {
		$pinValue = Pin::VALUE_HIGH == $pin->getValue() ? 1 : 0;

		$this->_local_client->execute(sprintf(self::GPIO_COMMAND_DIRECTION, $pin->getID(), $pin->getDirection()));
		$this->_local_client->execute(sprintf(self::GPIO_COMMAND_VALUE, $pin->getID(), $pinValue));
	}

	/**
	 * @return PinsCollection
	 */
	private function _loadPins() {
		if (null !== $this->_pins) {
			return $this->_pins;
		}

		$results = $this->_local_client->executeWithReturn(self::GPIO_COMMAND_READALL);
		$results = explode("\n", $results);
		$results = array_slice($results, 3, -2);

		$this->_pins = new PinsCollection();
		foreach ($results as $r) {
			$matches = explode('|', $r);
			$matches = array_map('trim', $matches);

			$pin = new Pin();
			$pin->setID($matches[1]);
			$pin->setName($matches[4]);
			$pin->setDirection($matches[5]);
			$pin->setValue((int)('High' == $matches[6]));

			$this->_pins->add($pin);
		}

		return $this->_pins;
	}

}