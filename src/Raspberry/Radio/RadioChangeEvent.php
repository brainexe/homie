<?php

namespace Raspberry\Radio;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use Raspberry\Radio\VO\RadioVO;

class RadioChangeEvent extends AbstractEvent {

	const CHANGE_RADIO = 'radio.change';

	/**
	 * @var RadioVO
	 */
	public $radio_vo;

	/**
	 * @var boolean
	 */
	public $status;

	/**
	 * @var boolean
	 */
	public $is_job;

	/**
	 * @param RadioVO $radio_vo
	 * @param boolean $status
	 */
	public function __construct(RadioVO $radio_vo, $status) {
		$this->event_name = self::CHANGE_RADIO;
		$this->radio_vo = $radio_vo;
		$this->status = $status;
	}
}