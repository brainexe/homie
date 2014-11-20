<?php

namespace Tests\Raspberry\DIC\SensorCompilerPass;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\DIC\SensorCompilerPass;
use Raspberry\Sensors\Sensors\SensorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @Covers Raspberry\DIC\SensorCompilerPass
 */
class SensorCompilerPassTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorCompilerPass
	 */
	private $_subject;

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
	 */
	private $_mock_container;

	public function setUp() {
		$this->_subject = new SensorCompilerPass();
		$this->_mock_container = $this->getMock(ContainerBuilder::class);
	}

	/**
	 *
	 */
	public function testProcess() {
		$sensor_builder = $this->getMock(Definition::class);
		$sensor_definition = $this->getMock(Definition::class);
		$sensor_id = 'sensor_1';

		$sensor = $this->getMock(SensorInterface::class);

		$this->_mock_container
			->expects($this->at(0))
			->method('getDefinition')
			->with('SensorBuilder')
			->will($this->returnValue($sensor_builder));

		$this->_mock_container
			->expects($this->at(1))
			->method('findTaggedServiceIds')
			->with(SensorCompilerPass::TAG)
			->will($this->returnValue([
			  $sensor_id => $sensor_definition
		  ]));

		$this->_mock_container
			->expects($this->at(2))
			->method('get')
			->with($sensor_id)
			->will($this->returnValue($sensor));

		$sensor->expects($this->once())
			   ->method('getSensorType')
			   ->will($this->returnValue($sensor_id));

		$sensor_builder
			->expects($this->once())
			->method('addMethodCall')
			->with('addSensor', [$sensor_id, new Reference($sensor_id)]);

		$this->_subject->process($this->_mock_container);
	}

}
