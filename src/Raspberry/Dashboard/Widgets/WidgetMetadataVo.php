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
	 * @var string
	 */
	public $id;

	/**
	 * @param string $id
	 * @param string $name
	 * @param string[] $parameters
	 */
	public function __construct($id, $name, array $parameters = []) {
		$this->name       = $name;
		$this->parameters = $parameters;
		$this->id         = $id;
	}
}
