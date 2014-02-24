<?php

namespace Raspberry\Dashboard;

/**
 * @Widget
 */
class TimeWidget extends AbstractWidget {

	/**
	 * @return string
	 */
	public function render() {
		return date('c');
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return 'Time';
	}

}
