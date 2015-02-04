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
    private $nodeId;

    public function setUp()
    {
        $this->nodeId = 5;

        $this->subject = new Node($this->nodeId);
    }

    public function testGetNodeId()
    {
        $actualResult = $this->subject->getNodeId();
        $this->assertEquals($this->nodeId, $actualResult);
    }

    public function testIsMaster()
    {
        $actualResult = $this->subject->isMaster();
        $this->assertFalse($actualResult);
    }

    public function testIsSlave()
    {
        $actualResult = $this->subject->isSlave();
        $this->assertTrue($actualResult);
    }
}
