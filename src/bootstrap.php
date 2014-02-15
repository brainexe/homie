<?php

use Matze\Core\Core;

define('ROOT', realpath(__DIR__ . '/..'));

include ROOT . '/vendor/autoload.php';

return Core::boot();