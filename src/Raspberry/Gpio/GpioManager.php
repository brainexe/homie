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
	private $_localClient;

	/**
	 * @var PinGateway
	 */
	private $_pinGateway;

	/**
	 * @var PinLoader
	 */
	private $_pinLoader;

	/**
	 * @Inject({"@PinGateway", "@RaspberryClient", "@PinLoader"})
	 * @param PinGateway $pin_gateway
	 * @param ClientInterface $local_client
	 * @param PinLoader $pinLoader
	 */
	public function __construct(PinGateway $pin_gateway, ClientInterface $local_client, PinLoader $pinLoader) {
		$this->_pinGateway  = $pin_gateway;
		$this->_localClient = $local_client;
		$this->_pinLoader   = $pinLoader;
	}

	/**
	 * @return PinsCollection
	 */
	public function getPins() {
		$descriptions = $this->_pinGateway->getPinDescriptions();

		$pins = $this->_pinLoader->loadPins();

		foreach ($pins->getAll() as $pin) {
			/** @var Pin $pin */
			if (!empty($descriptions[$pin->getId()])) {
				$pin->setDescription($descriptions[$pin->getId()]);
			}
		}

		return $pins;
	}

	/**
	 * @param integer $id
	 * @param string $status
	 * @param boolean $value
	 * @return Pin
	 */
	public function setPin($id, $status, $value) {
		$pin = $this->_pinLoader->loadPin($id);

		$pin->setDirection($status ? 'out' : 'in');
		$pin->setValue($value ? Pin::VALUE_HIGH : Pin::VALUE_LOW);

		$this->_updatePin($pin);

		return $pin;
	}

	/**
	 * @param Pin $pin Pin
	 */
	private function _updatePin(Pin $pin) {
		$pinValue = Pin::VALUE_HIGH == $pin->getValue() ? 1 : 0;

		$this->_localClient->execute(sprintf(self::GPIO_COMMAND_DIRECTION, $pin->getID(), $pin->getDirection()));
		$this->_localClient->execute(sprintf(self::GPIO_COMMAND_VALUE, $pin->getID(), $pinValue));
	}

}