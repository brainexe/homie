<?php

namespace Raspberry\Dashboard\Widgets;

use Raspberry\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class TimeWidget extends AbstractWidget {

	const TYPE = 'time';

	/**
	 * @return string
	 */
	public function getId() {
		return self::TYPE;
	}
}
