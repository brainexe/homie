<?php

namespace Raspberry\Dashboard;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class DashboardGateway
{

    const KEY_META = 'META__';

    use RedisTrait;
    use IdGeneratorTrait;

    const DASHBOARD_KEY = 'dashboard:%s';
    const DASHBOARD_IDS = 'dashboard_ids';

    /**
     * @return DashboardVo[]
     */
    public function getDashboards()
    {
        $redis = $this->getRedis();

        $dashboards = [];
        $dashboardIds = $redis->sMembers(self::DASHBOARD_IDS);
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

        $widgetsRaw = $this->getRedis()->hGetAll($this->getKey($dashboardId));

        foreach ($widgetsRaw as $id => $widgetRaw) {
            switch ($id) {
                case 'name':
                    $dashboard->name = $widgetRaw;
                    continue 2;
            }

            $widget = json_decode($widgetRaw, true);
            $widget['id']   = $id;
            $widget['open'] = true;
            $dashboard->widgets[] = $widget;
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
        $this->getRedis()->hSet($this->getKey($dashboardId), $newId, json_encode($payload));
        $this->getRedis()->sAdd(self::DASHBOARD_IDS, $dashboardId);
    }

    /**
     * @param integer $dashboardId
     * @param integer $widgetId
     */
    public function deleteWidget($dashboardId, $widgetId)
    {
        $this->getRedis()->hDel($this->getKey($dashboardId), $widgetId);
        $this->getRedis()->sRem(self::DASHBOARD_IDS, $dashboardId);
    }

    /**
     * @param integer $dashboardId
     * @return string
     */
    private function getKey($dashboardId)
    {
        return sprintf(self::DASHBOARD_KEY, $dashboardId);
    }

    /**
     * @param $dashboardId
     * @param $name
     */
    public function updateDashboard($dashboardId, $name)
    {
        $this->getRedis()->hset($this->getKey($dashboardId), 'name', $name);
    }
}
