<?php

namespace Tests\Raspberry\Node;

use PHPUnit_Framework_TestCase;

use Raspberry\Node;

/**
 * @Covers Raspberry\Node
 */
class NodeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Node
     */
    private $subject;

    /**
     * @var integer
     */
    private $node_id;

    public function setUp()
    {
        $this->node_id = 5;

        $this->subject = new Node($this->node_id);
    }

    public function testGetNodeId()
    {
        $actual_result = $this->subject->getNodeId();
        $this->assertEquals($this->node_id, $actual_result);
    }

    public function testIsMaster()
    {
        $actual_result = $this->subject->isMaster();
        $this->assertFalse($actual_result);
    }

    public function testIsSlave()
    {
        $actual_result = $this->subject->isSlave();
        $this->assertTrue($actual_result);
    }
}
