<?php

namespace Homie;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

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
     * @return int
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @return bool
     */
    public function isMaster()
    {
        return $this->nodeId == 0;
    }

    /**
     * @return bool
     */
    public function isSlave()
    {
        return $this->nodeId > 0;
    }
}
