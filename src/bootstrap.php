<?php

use Matze\Core\DependencyInjection\GlobalCompilerPass;
use Monolog\ErrorHandler;
use Monolog\Logger;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Loader\AnnotationLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

chdir(__DIR__);

include '../vendor/autoload.php';
date_default_timezone_set('Europe/Berlin');

if (file_exists('../cache/dic.php')) {
	include '../cache/dic.php';
	$dic = new DIC();
} else {
	$dic = rebuild_dic();
}
/** @var Logger $logger */
$logger = $dic->get('Monolog.Logger');

$error_handler = new ErrorHandler($logger);
$error_handler->registerErrorHandler();
$error_handler->registerExceptionHandler();
$error_handler->registerFatalHandler();

function rebuild_dic () {
	require_once "../vendor/matze/slim-losolib/Loso/Bundle/DiAnnotationsBundle/DependencyInjection/Annotations/Service.php";
	require_once "../vendor/matze/slim-losolib/Loso/Bundle/DiAnnotationsBundle/DependencyInjection/Annotations/Inject.php";
	require_once "../vendor/matze/slim-losolib/Loso/Bundle/DiAnnotationsBundle/DependencyInjection/Loader/AnnotationLoader.php";

	$container_builder = new ContainerBuilder();
	$annotation_loader = new AnnotationLoader($container_builder);
	$annotation_loader->load('../src/');

	$loader = new XmlFileLoader($container_builder, new FileLocator('../config'));
	$loader->load('services.xml');
	$loader->load('config.default.xml');
	if (file_exists('config/config.xml')) {
		$loader->load('config.xml');
	}

	$container_builder->addCompilerPass(new GlobalCompilerPass());
//	$container_builder->addCompilerPass(new ConsoleCompilerPass());
//	$container_builder->addCompilerPass(new ControllerCompilerPass());
//	$container_builder->addCompilerPass(new SensorCompilerPass());
	$container_builder->compile();

	$dumper = new PhpDumper($container_builder);
	$container_content = $dumper->dump(['class' => 'DIC']);
	file_put_contents('../cache/dic.php', $container_content);

	return $container_builder;
}

return $dic;
