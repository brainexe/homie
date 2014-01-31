<?php

use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;

chdir(__DIR__);

include "../vendor/autoload.php";

/** @var Container $dic */
$dic = include "Container.php";

$file_logger = new StreamHandler('../logs/error.log', Logger::DEBUG);

/** @var Logger $logger */
$logger = $dic->get('Monolog.Logger');
$logger->pushHandler($file_logger);

$error_handler = new ErrorHandler($logger);
$error_handler->registerErrorHandler();
$error_handler->registerExceptionHandler();
$error_handler->registerFatalHandler();

return $dic;