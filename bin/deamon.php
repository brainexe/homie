<?php

use Predis\Client;
use Raspberry\Radio\RadioController;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @var ContainerBuilder $dic */
$dic = include __DIR__ . '/../src/bootstrap.php';

/** @var RadioController $radio_controller */
/** @var Client $predis */
$radio_controller = $dic->get('RadioController');
$predis = $dic->get('Predis');

// Initialize a new pubsub context
$pub_sub = $predis->pubSubLoop();

// Subscribe to your channels
$pub_sub->subscribe('radio_changes');

foreach ($pub_sub as $change) {
	if ($change->kind !== 'message') {
		continue;
	}
	$payload = unserialize($change->payload);
	if (!$payload) {
		continue;
	}

	try {
		$radio_controller->setStatus($payload['code'], $payload['pin'], $payload['status']);
	} catch (RuntimeException $e) {
		echo $e->getMessage()."\n";
	}

	echo $payload['code'] ." - ". $payload['pin'] ." - ". $payload['status'] ."\n";
}