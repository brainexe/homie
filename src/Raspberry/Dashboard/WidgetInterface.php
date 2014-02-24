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
	public function render();

	/**
	 * @return string
	 */
	public function getTitle();
} 
