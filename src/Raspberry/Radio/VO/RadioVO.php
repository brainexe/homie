<?php

namespace Raspberry\Radio\VO;

use Matze\Core\Util\AbstractVO;

class RadioVO extends AbstractVO {

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $code;

	/**
	 * @var integer
	 */
	public $pin;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $description;

}