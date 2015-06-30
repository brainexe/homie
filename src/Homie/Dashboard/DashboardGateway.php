<?php

namespace Homie\Dashboard;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class DashboardGateway
{

    use RedisTrait;
    use IdGeneratorTrait;

    const META_KEY   = 'dashboard:meta:%s';
    const WIDGET_KEY = 'dashboard:widgets:%s';
    const IDS_KEY    = 'dashboard:ids';

    /**
     * @return DashboardVo[]
     */
    public function getDashboards()
    {
        $redis = $this->getRedis();

        $dashboards = [];
        $dashboardIds = $redis->sMembers(self::IDS_KEY);
        foreach ($dashboardIds as $dashboardId) {
            $dashboards[$dashboardId] = $this->getDashboard($dashboardId);
        }

        return $dashboards;
    }
    /**
     * @param integer $dashboardId
     * @return DashboardVo
     */
    public function getDashboard($dashboardId)
    {
        $dashboard = new DashboardVo();
        $dashboard->dashboardId = $dashboardId;

        $widgetsRaw = $this->getRedis()->hGetAll($this->getWidgetKey($dashboardId));
        $meta       = $this->getRedis()->hGetAll($this->getMetaKey($dashboardId));

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
     * @param integer $dashboardId
     * @param array $payload
     */
    public function addWidget($dashboardId, array $payload)
    {
        $newId = $this->generateRandomNumericId();
        $payload['id']   = $newId;
        $payload['open'] = true;

        $this->updateWidget($dashboardId, $newId, $payload);
    }

    /**
     * @param int $dashboardId
     * @param int $widgetId
     * @param array $payload
     */
    public function updateWidget($dashboardId, $widgetId, array $payload)
    {
        $this->getRedis()->hSet($this->getWidgetKey($dashboardId), $widgetId, json_encode($payload));
    }

    /**
     * @param integer $dashboardId
     * @param integer $widgetId
     */
    public function deleteWidget($dashboardId, $widgetId)
    {
        $this->getRedis()->hDel($this->getWidgetKey($dashboardId), $widgetId);
    }

    /**
     * @param int $dashboardId
     * @param array $metadata
     */
    public function addDashboard($dashboardId, array $metadata)
    {
        $this->getRedis()->sAdd(self::IDS_KEY, $dashboardId);
        $this->updateMetadata($dashboardId, $metadata);
    }

    /**
     * @param int $dashboardId
     * @param array $payload
     */
    public function updateMetadata($dashboardId, array $payload)
    {
        $this->getRedis()->hmset($this->getMetaKey($dashboardId), $payload);
    }

    /**
     * @param int $dashboardId
     */
    public function delete($dashboardId)
    {
        $this->getRedis()->del($this->getWidgetKey($dashboardId));
        $this->getRedis()->del($this->getMetaKey($dashboardId));
        $this->getRedis()->sRem(self::IDS_KEY, $dashboardId);
    }

    /**
     * @param integer $dashboardId
     * @return string
     */
    private function getWidgetKey($dashboardId)
    {
        return sprintf(self::WIDGET_KEY, $dashboardId);
    }

    /**
     * @param integer $dashboardId
     * @return string
     */
    private function getMetaKey($dashboardId)
    {
        return sprintf(self::META_KEY, $dashboardId);
    }
}
