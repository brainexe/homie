<?php

namespace Raspberry\Radio;

use Matze\Core\EventDispatcher\AbstractEvent;
use Raspberry\Radio\VO\RadioVO;

class RadioChangeEvent extends AbstractEvent {

	const NAME = 'radio.change';

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
	 * @param boolean $is_job
	 */
	public function __construct(RadioVO $radio_vo, $status, $is_job = false) {
		$this->event_name = self::NAME;
		$this->radio_vo = $radio_vo;
		$this->status = $status;
		$this->is_job = $is_job;
	}
}