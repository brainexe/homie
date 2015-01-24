<?php

use BrainExe\Core\Core;

include __DIR__ . '/../src/bootstrap.php';

global $dic;

$core = new Core();
$dic = $core->boot();
