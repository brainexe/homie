<?php

namespace Raspberry\Dashboard;

use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @service(public=false)
 */
class DashboardGateway
{

    use RedisTrait;
    use IdGeneratorTrait;

    const REDIS_DASHBOARD = 'dashboard:%s';

    /**
     * @param integer $userId
     * @return array[]
     */
    public function getDashboard($userId)
    {
        $dashboard = [];

        $widgetsRaw = $this->getRedis()->hGetAll($this->getKey($userId));

        foreach ($widgetsRaw as $id => $widgetRaw) {
            $widget = json_decode($widgetRaw, true);
            $widget['id']   = $id;
            $widget['open'] = true;
            $dashboard[] = $widget;
        }

        return $dashboard;
    }

    /**
     * @param integer $userId
     * @param array $payload
     */
    public function addWidget($userId, array $payload)
    {
        $newId = $this->generateRandomNumericId();
        $this->getRedis()->HSET($this->getKey($userId), $newId, json_encode($payload));
    }

    /**
     * @param integer $userId
     * @param integer $widgetId
     */
    public function deleteWidget($userId, $widgetId)
    {
        $this->getRedis()->HDEL($this->getKey($userId), $widgetId);

    }

    /**
     * @param integer $userId
     * @return string
     */
    private function getKey($userId)
    {
        return sprintf(self::REDIS_DASHBOARD, $userId);
    }
}
