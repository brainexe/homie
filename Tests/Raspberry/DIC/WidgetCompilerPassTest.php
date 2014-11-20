<?php

namespace Tests\Raspberry\DIC\WidgetCompilerPass;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Dashboard\WidgetInterface;
use Raspberry\DIC\WidgetCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @Covers Raspberry\DIC\WidgetCompilerPass
 */
class WidgetCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var WidgetCompilerPass
	 */
	private $_subject;

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
	 */
	private $_mock_container;

	public function setUp() {
		$this->_subject = new WidgetCompilerPass();
		$this->_mock_container = $this->getMock(ContainerBuilder::class);
	}

	public function testProcess() {
		$widget_factory = $this->getMock(Definition::class);
		$widget_definition = $this->getMock(Definition::class);
		$widget_id = 'widget_1';

		$widget = $this->getMock(WidgetInterface::class);

		$this->_mock_container
			->expects($this->at(0))
			->method('getDefinition')
			->with('WidgetFactory')
			->will($this->returnValue($widget_factory));

		$this->_mock_container
			->expects($this->at(1))
			->method('findTaggedServiceIds')
			->with(WidgetCompilerPass::TAG)
			->will($this->returnValue([
				$widget_id => $widget_definition
			]));

		$this->_mock_container
			->expects($this->at(2))
			->method('get')
			->with($widget_id)
			->will($this->returnValue($widget));

		$widget->expects($this->once())
			->method('getId')
			->will($this->returnValue($widget_id));

		$widget_factory
			->expects($this->once())
			->method('addMethodCall')
			->with('addWidget', [$widget_id, new Reference($widget_id)]);

		$this->_subject->process($this->_mock_container);
	}

}
