<?php

namespace Raspberry\Radio;

use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
class Radios {

	/**
	 * @var RadioGateway
	 */
	private $_radio_gateway;

	/**
	 * @var RadioController
	 */
	private $radio_controller;

	/**
	 * @DI\Inject({"@RadioController", "@RadioGateway"})
	 */
	public function __construct(RadioController $radio_controller, RadioGateway $radio_gateway) {
		$this->radio_controller = $radio_controller;
		$this->_radio_gateway = $radio_gateway;
	}

	/**
	 * @return array[]
	 */
	public function getRadios() {
		$radios_raw = $this->_radio_gateway->getRadios();
		$radios_formatted = [];

		foreach ($radios_raw as $radio_raw) {
			$radio_raw['status'] = $this->radio_controller->getStatus($radio_raw['code'], $radio_raw['pin']);
			$radios_formatted[$radio_raw['id']] = $radio_raw;
		}

		return $radios_formatted;
	}

}