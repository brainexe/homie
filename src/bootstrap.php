<?php

use Matze\Core\Core;

define('ROOT', realpath(__DIR__ . '/..'));

chdir(ROOT);

include ROOT . '/vendor/autoload.php';

return Core::boot();