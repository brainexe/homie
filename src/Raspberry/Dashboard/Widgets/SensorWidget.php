<?php

namespace Raspberry\Dashboard\Widgets;

use Matze\Core\Application\UserException;
use Raspberry\Dashboard\AbstractWidget;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\Sensors\SensorInterface;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class SensorWidget extends AbstractWidget {

	const TYPE = 'sensor';

	/**
	 * @return string
	 */
	public function getId() {
		return self::TYPE;
	}

	/**
	 * @param array $payload
	 * @return mixed|void
	 * @throws UserException
	 */
	public function create(array $payload) {
		if (empty($payload['sensor_id'])) {
			throw new UserException("No sensor_id passed");
		}
	}
}
