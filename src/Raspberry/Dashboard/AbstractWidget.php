<?php

namespace Raspberry\Dashboard;

abstract class AbstractWidget implements WidgetInterface {

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
