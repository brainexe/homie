<?php

namespace Raspberry\Radio;

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
	 * @param RadioController$radio_controller
	 */
	public function setRadioController($radio_controller) {
		$this->radio_controller = $radio_controller;
	}

	/**
	 * @param RadioGateway $radio_gateway
	 */
	public function setRadioGateway($radio_gateway) {
		$this->_radio_gateway = $radio_gateway;
	}

	public function getRadios() {
		$radios_raw = $this->_radio_gateway->getRadios();

		foreach ($radios_raw as $radio_raw) {
			$radio_raw['status'] = $this->radio_controller->getStatus($radio_raw['pin']);
		}

		return $radios_raw;
	}

} 