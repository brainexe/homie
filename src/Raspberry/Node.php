<?php

namespace Raspberry;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Annotations\Annotations\Value;

/**
 * @Service(public=false)
 */
class Node
{

    /**
     * @var integer
     */
    private $nodeId;

    /**
     * @Inject("%node.id%")
     * @param $nodeId
     */
    public function __construct($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * @return integer
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @return boolean
     */
    public function isMaster()
    {
        return $this->nodeId == 0;
    }

    /**
     * @return boolean
     */
    public function isSlave()
    {
        return $this->nodeId > 0;
    }
}
