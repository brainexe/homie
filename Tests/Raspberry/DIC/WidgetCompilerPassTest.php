<?php

namespace Tests\Raspberry\DIC\WidgetCompilerPass;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\DIC\WidgetCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @Covers Raspberry\DIC\WidgetCompilerPass
 */
class WidgetCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var WidgetCompilerPass
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new WidgetCompilerPass();
	}

	public function testProcess() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$container = new ContainerBuilder();

		$widget_1 = new Definition();
		$widget_1->addTag(WidgetCompilerPass::TAG);

		$widget_factory = new Definition();

		$container->setDefinition('WidgetFactory', $widget_factory);
		$container->setDefinition('Widget1', $widget_1);

		$this->_subject->process($container);

		print_r($container->get('WidgetFactory'));
		print_r($container);
	}

}
