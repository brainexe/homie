<?php

namespace Raspberry\Dashboard;

use Matze\Core\Traits\TwigTrait;

abstract class AbstractWidget implements WidgetInterface {

	use TwigTrait;

	/**
	 * {@inheritdoc}
	 */
	public function create(array $payload) {
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate(array $payload) {
		return true;
	}

} 
