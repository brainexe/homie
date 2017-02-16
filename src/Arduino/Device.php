<?php

namespace Homie\Arduino;

interface Device
{
    /**
     * @param SerialEvent $event
     */
    public function sendSerial(SerialEvent $event);
}
