<?php

namespace Tests\Homie\Expression;

use Homie\Expression\Entity;
use Homie\Expression\Listener;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Expression\Entity
 */
class EntityTest extends TestCase
{

    public function testSerialize()
    {
        $subject = new Entity();
        $subject->expressionId = 1;
        $subject->actions = ['actions'];
        $subject->conditions = ['conditions'];
        $subject->payload = ['payload'];
        $subject->enabled = true;

        $expected = [
            'expressionId' => 1,
            'actions' => ['actions'],
            'conditions' => ['conditions'],
            'payload' => ['payload'],
            'enabled' => true
        ];

        $this->assertEquals($expected, $subject->jsonSerialize());
    }
}
