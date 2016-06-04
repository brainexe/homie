<?php

namespace Tests\Homie\Expression\Controller;

use Homie\Expression\Controller\Controller;
use Homie\Expression\Entity;
use Homie\Expression\Gateway;
use Homie\Expression\Language;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Language|MockObject
     */
    private $language;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    public function setup()
    {
        $this->gateway  = $this->createMock(Gateway::class);
        $this->language = $this->createMock(Language::class);

        $this->subject = new Controller(
            $this->gateway,
            $this->language
        );
    }

    public function testEvaluate()
    {
        $this->language
            ->expects($this->once())
            ->method('evaluate')
            ->with('1 + 2')
            ->willReturn(3);

        $request = new Request();
        $request->query->set('expression', '1 + 2');
        $actual = $this->subject->evaluate($request);

        $expected = 3;
        $this->assertEquals($expected, $actual);
    }

    public function testDelete()
    {
        $expressionId = 42;
        $this->gateway
            ->expects($this->once())
            ->method('delete')
            ->with($expressionId)
            ->willReturn(true);

        $request = new Request();
        $actual = $this->subject->delete($request, $expressionId);

        $this->assertTrue($actual);
    }

    public function testLoad()
    {
        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn(['expressions']);

        $request = new Request();
        $request->query->set('expression', '1 + 2');
        $actual = $this->subject->load();

        $this->assertEquals(['expressions'], $actual);
    }

    public function testEvents()
    {
        $actual = $this->subject->events();

        $this->assertInternalType('array', $actual);
    }

    public function testFunctions()
    {
        $actual = $this->subject->functions();

        $this->assertInternalType('array', $actual);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage No expression id defined
     */
    public function testSaveWithoutId()
    {
        $request = new Request();

        $this->subject->save($request);
    }

    public function testSaveAction()
    {
        $request = new Request();
        $request->request->set('expressionId', 42);
        $request->request->set('actions', ['action1']);
        $request->request->set('conditions', ['condition1']);
        $request->request->set('enables', true);

        $existingEntity = new Entity();
        $existingEntity->compiledCondition = ['compiled'];

        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
                42 => $existingEntity
            ]);

        $entity = new Entity();
        $entity->compiledCondition = ['compiled'];
        $entity->conditions = ['condition1'];
        $entity->actions    = ['action1'];
        $entity->enabled    = false;

        $this->language
            ->expects($this->exactly(2))
            ->method('parse');

        $this->gateway
            ->expects($this->once())
            ->method('save')
            ->with($entity);

        $actual = $this->subject->save($request);

        $this->assertEquals($entity, $actual);
    }

    public function testSaveNew()
    {
        $request = new Request();
        $request->request->set('expressionId', 'new');
        $request->request->set('actions', ['action1']);
        $request->request->set('conditions', ['condition1']);
        $request->request->set('enables', true);

        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
            ]);

        $entity = new Entity();
        $entity->expressionId = 'new';
        $entity->conditions = ['condition1'];
        $entity->actions    = ['action1'];
        $entity->enabled    = false;

        $this->language
            ->expects($this->exactly(2))
            ->method('parse');

        $this->gateway
            ->expects($this->once())
            ->method('save')
            ->with($entity);

        $actual = $this->subject->save($request);

        $this->assertEquals($entity, $actual);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage No actions defined
     */
    public function testSaveWithoutAction()
    {
        $request = new Request();
        $request->request->set('expressionId', 42);
        $request->request->set('actions', []);
        $request->request->set('conditions', ['condition1']);
        $request->request->set('enables', true);

        $existingEntity = new Entity();
        $existingEntity->compiledCondition = ['compiled'];

        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
                42 => $existingEntity
            ]);

        $entity = new Entity();
        $entity->compiledCondition = ['compiled'];
        $entity->conditions = ['condition1'];
        $entity->actions    = ['action1'];
        $entity->enabled    = false;

        $this->subject->save($request);
    }
}
