<?php

namespace Tests\Homie\Node;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Node;

class NodeTest extends TestCase
{

    /**
     * @var Node
     */
    private $subject;

    public function testSetterGetter()
    {
        $nodeId  = 42;
        $type    = 'type';
        $name    = 'name';
        $options = ['options'];

        $this->subject = new Node($nodeId, $type, $name, $options);
        $this->assertEquals($nodeId, $this->subject->getNodeId());

        $this->subject->setOptions($options);
        $this->subject->setName('newName');

        $this->assertEquals($type, $this->subject->getType());

        $expected = [
            'nodeId' => $nodeId,
            'name' => 'newName',
            'options' => $options,
            'type' => $type,
        ];

        $this->assertEquals($expected, $this->subject->jsonSerialize());
        $this->assertEquals($options, $this->subject->getOptions());
    }
}
