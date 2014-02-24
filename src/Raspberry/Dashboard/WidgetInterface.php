<?php

namespace Raspberry\Dashboard;

interface WidgetInterface {

	/**
	 * @return string
	 */
	public function getId();

	/**
	 * @return string
	 */
	public function renderWidget();

	public function create(array $payload);

	public function validate(array $payload);
}
