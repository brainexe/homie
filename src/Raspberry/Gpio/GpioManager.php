<?php

namespace Raspberry\Gpio;

use Raspberry\Client\LocalClient;
use Raspberry\Client\LocalManager;
use Raspberry\Client\Pin;
use Raspberry\Client\PinsCollection;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
class GpioManager {

	/**
	 * @var LocalClient
	 */
	private $_local_client;

	/**
	 * @var PinGateway
	 */
	private $_pin_gateway;

	/**
	 * @DI\Inject({"@PinGateway", "@LocalClient"})
	 */
	public function __construct(PinGateway $pin_gateway, LocalClient $local_client) {
		$this->_pin_gateway = $pin_gateway;
		$this->_local_client = $local_client;
	}

	/**
	 * @return PinsCollection
	 */
	public function getPins() {
		$descriptions = $this->_pin_gateway->getPinDescriptions();
		try {
			$manager = new LocalManager($this->_local_client);
			$collection = $manager->getPins();
		} catch (\RuntimeException $e) {
			$collection = new PinsCollection();

			$pin = new Pin();
			$pin->setID(2);
			$pin->setName('GPIO 2');
			$pin->setDirection('OUT');
			$pin->setValue(true);
			$collection->add($pin);

			$pin = new Pin();
			$pin->setName('GPIO 3');
			$pin->setID(3);
			$pin->setDirection('IN');
			$pin->setValue(false);
			$collection->add($pin);
		}

		foreach ($collection as $pin) {
			/** @var Pin $pin */
			if (!empty($descriptions[$pin->getId()])) {
				$pin->setDescription($descriptions[$pin->getId()]);
			}
		}

		return $collection;
	}

	/**
	 * @param integer $id
	 * @param string $status
	 * @param boolean $value
	 */
	public function setPin($id, $status, $value) {
		$manager = new LocalManager($this->_local_client);
		$pin = $manager->getPins()->get($id);

		$pin->setDirection($status ? 'out' : 'in');
		$pin->setValue($value ? Pin::VALUE_HIGH : Pin::VALUE_LOW);

		$manager->update($pin);
	}

}