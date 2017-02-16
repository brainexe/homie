<?php

namespace Tests\Homie\Node;

use PHPUnit\Framework\TestCase;
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
        $options = ['key' => 'value'];

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
        $this->assertEquals('value', $this->subject->getOption('key'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid option: undefinedKey
     */
    public function testUndefinedOption()
    {
        $nodeId  = 42;
        $type    = 'type';
        $name    = 'name';
        $options = ['key' => 'value'];

        $this->subject = new Node($nodeId, $type, $name, $options);

        $this->assertEquals('value', $this->subject->getOption('key'));

        $this->subject->getOption('undefinedKey');
    }
}
