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
        $address = 'address';

        $this->subject = new Node($nodeId, $type, $name, $address);
        $this->assertEquals($nodeId, $this->subject->getNodeId());

        $this->subject->setAddress('newAddress');
        $this->subject->setName('newName');

        $this->assertEquals($type, $this->subject->getType());

        $expected = [
            'nodeId' => $nodeId,
            'name' => 'newName',
            'address' => 'newAddress',
            'type' => $type,
        ];

        $this->assertEquals($expected, $this->subject->jsonSerialize());
    }
}
