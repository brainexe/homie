<?php

namespace Raspberry\Client;

use Sly\RPIManager\IO\GPIO\Manager;
use Sly\RPIManager\IO\GPIO\Model\Pin;

class LocalManager extends Manager {

	/**
	 * Init.
	 */
	private function init()
	{
		$results = $this->client->execute(self::GPIO_COMMAND_READALL);
		$results = explode("\n", $results);

		print_r($results);
		foreach ($results as $r) {
			if (preg_match('/^\|(.*?)\|(.*?)\|(.*?)\|(.*?)\|(.*?)\|$/', $r, $matches))
			{
				$pin = new Pin();
				$pin->setID(trim($matches[1]));
				$pin->setName(trim($matches[3]));
				$pin->setDirection(trim($matches[1]));
				$pin->setValue(Pin::VALUE_HIGH == trim($matches[1]) ? true : false);

				$this->pins->add($pin);
			}
		}
	}
} 