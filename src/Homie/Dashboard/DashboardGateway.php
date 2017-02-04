<?php

namespace Homie\Dashboard;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use Generator;

/**
 * @Service
 */
class DashboardGateway
{

    use RedisTrait;
    use IdGeneratorTrait;

    const META_KEY   = 'dashboard:meta:%s';
    const WIDGET_KEY = 'dashboard:widgets:%s';
    const IDS_KEY    = 'dashboard:ids';

    /**
     * @return Generator|DashboardVo[]
     */
    public function getDashboards()
    {
        $redis = $this->getRedis();

        $dashboardIds = $redis->smembers(self::IDS_KEY);
        foreach ($dashboardIds as $dashboardId) {
            yield (int)$dashboardId => $this->getDashboard($dashboardId);
        }
    }
    /**
     * @param int $dashboardId
     * @return DashboardVo
     */
    public function getDashboard(int $dashboardId) : DashboardVo
    {
        $dashboard = new DashboardVo();
        $dashboard->dashboardId = $dashboardId;

        $widgetsRaw = $this->getRedis()->hgetall($this->getWidgetKey($dashboardId));
        $meta       = $this->getRedis()->hgetall($this->getMetaKey($dashboardId));

        foreach ($widgetsRaw as $widgetRaw) {
            $widget = json_decode($widgetRaw, true);
            $dashboard->widgets[] = $widget;
        }

        foreach ($meta as $key => $value) {
            $dashboard->$key = $value;
        }

        return $dashboard;
    }

    /**
     * @param int $dashboardId
     * @param array $payload
     */
    public function addWidget(int $dashboardId, array $payload)
    {
        $newId = $this->generateUniqueId('widget');
        $payload['id']   = $newId;
        $payload['open'] = true;

        $this->updateWidget($dashboardId, $newId, $payload);
    }

    /**
     * @param int $dashboardId
     * @param int $widgetId
     * @param array $payload
     */
    public function updateWidget(int $dashboardId, int $widgetId, array $payload)
    {
        $this->getRedis()->hset($this->getWidgetKey($dashboardId), $widgetId, json_encode($payload));
    }

    /**
     * @param int $dashboardId
     * @param int $widgetId
     */
    public function deleteWidget(int $dashboardId, int $widgetId)
    {
        $this->getRedis()->hdel($this->getWidgetKey($dashboardId), [$widgetId]);
    }

    /**
     * @param int $dashboardId
     * @param array $metadata
     */
    public function addDashboard(int $dashboardId, array $metadata)
    {
        $this->getRedis()->sadd(self::IDS_KEY, [$dashboardId]);
        $this->updateMetadata($dashboardId, $metadata);
    }

    /**
     * @param int $dashboardId
     * @param array $payload
     */
    public function updateMetadata(int $dashboardId, array $payload)
    {
        $this->getRedis()->hmset($this->getMetaKey($dashboardId), $payload);
    }

    /**
     * @param int $dashboardId
     */
    public function delete(int $dashboardId)
    {
        $this->getRedis()->del($this->getWidgetKey($dashboardId));
        $this->getRedis()->del($this->getMetaKey($dashboardId));
        $this->getRedis()->srem(self::IDS_KEY, $dashboardId);
    }

    /**
     * @param int $dashboardId
     * @return string
     */
    private function getWidgetKey(int $dashboardId)
    {
        return sprintf(self::WIDGET_KEY, $dashboardId);
    }

    /**
     * @param int $dashboardId
     * @return string
     */
    private function getMetaKey(int $dashboardId)
    {
        return sprintf(self::META_KEY, $dashboardId);
    }
}
