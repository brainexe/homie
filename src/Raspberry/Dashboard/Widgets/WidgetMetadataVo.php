<?php

namespace Raspberry\Dashboard\Widgets;

class WidgetMetadataVo {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string[]
	 */
	public $parameters = [];

	/**
	 * @param string $name
	 * @param string[] $parameters
	 */
	public function __construct($name, array $parameters = []) {
		$this->name = $name;
		$this->parameters = $parameters;
	}
}
