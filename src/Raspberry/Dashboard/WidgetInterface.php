<?php

namespace Raspberry\Dashboard;

interface WidgetInterface {

	/**
	 * @return string
	 */
	public function getId();

	/**
	 * @param array $payload
	 * @return mixed
	 */
	public function create(array $payload);

	/**
	 * @param array $payload
	 * @return mixed
	 */
	public function validate(array $payload);
}
