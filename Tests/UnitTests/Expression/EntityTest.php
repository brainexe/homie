<?php

namespace Tests\Homie\Expression;

use Homie\Expression\Entity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Expression\Entity
 */
class EntityTest extends TestCase
{

    public function testSerialize()
    {
        $subject = new Entity();
        $subject->expressionId = 1;
        $subject->actions = ['actions'];
        $subject->conditions = ['conditions'];
        $subject->enabled = true;

        $expected = [
            'expressionId' => 1,
            'actions' => ['actions'],
            'conditions' => ['conditions'],
            'enabled' => true
        ];

        $this->assertEquals($expected, $subject->jsonSerialize());
    }
}
