<?php

namespace Raspberry\Radio;

/**
 * @Service(public=false)
 */
class Radios {

	/**
	 * @var RadioGateway
	 */
	private $_radio_gateway;

	/**
	 * @Inject("@RadioGateway")
	 */
	public function __construct(RadioGateway $radio_gateway) {
		$this->_radio_gateway = $radio_gateway;
	}

	/**
	 * @param integer $radio_id
	 * @return array
	 */
	public function getRadio($radio_id) {
		return $this->_radio_gateway->getRadio($radio_id);
	}

	/**
	 * @return array[]
	 */
	public function getRadios() {
		return $this->_radio_gateway->getRadios();
	}

}