
App.controller('DashboardController', /*@ngInject*/ function($scope, $uibModal, $q, Dashboard, UserManagementSettings, lodash) {
    $scope.editMode = false;
    $scope.currentWidth = 0; // todo fix rows in dashboard

    function selectDashboard(dashboard, notSaveOption) {
        if (!notSaveOption) {
            UserManagementSettings.set('selectedDashboardId', dashboard.dashboardId);
        }

        if (dashboard.order) {
            var order = dashboard.order.split(',').map(Number);

            dashboard.widgets.sort(function(a, b) {
                var indexA = order.indexOf(a.id);
                var indexB = order.indexOf(b.id);
                return indexA > indexB;
            });
        }

        $scope.dashboard = dashboard;
    }

    $q.all([
        Dashboard.getCachedMetadata(),
        Dashboard.getDashboards(),
        UserManagementSettings.getAll()
    ]).then(function(data) {
        var metadata     = data[0].data,
            dashboards   = data[1].data,
            settings     = data[2].data,
            dashboardIds = Object.keys(dashboards.dashboards).map(Number),
            selectedId;

        $scope.dashboards = dashboards.dashboards;
        $scope.widgets    = metadata.widgets;

        selectedId = ~~settings.selectedDashboardId;

        if (!selectedId || dashboardIds.indexOf(selectedId) === -1) {
            selectedId = dashboardIds[0];
        }

        if (selectedId) {
            selectDashboard(dashboards.dashboards[selectedId], true);
        }
    });

    $scope.dragControlListeners = {
        orderChanged (event) {
            var items = event.dest.sortableScope.modelValue;
            var order = items.map((item) => item.id);

            $scope.dashboard.order = order.join(',');

            Dashboard.saveOrder($scope.dashboard.dashboardId, order);
        }
    };

    $scope.metadata = function(widget, key) {
        var type = widget.type;

        if (!key) {
            // todo remove if not needed
            throw 'dashboards metadata() called without key!';
        }
        if (widget[key]) {
            return widget[key];
        }

        return $scope.widgets[type][key];
    };

	$scope.toggleWidget = function(widget, dashboard) {
        widget.open = !widget.open;
        Dashboard.updateWidget(dashboard.dashboardId, widget);
    };

	$scope.selectDashboard = function(dashboard) {
        selectDashboard($scope.dashboards[dashboard.dashboardId]);
    };

	$scope.toggleEditMode = function () {
        $scope.editMode = !$scope.editMode;
    };

    $scope.openModal = function(dashboards) {
        var modal = $uibModal.open({
			templateUrl: "/templates/widgets/new.html",
			controller: "NewWidgetController",
			resolve: {
                widgets:          () => $scope.widgets,
                dashboards:       () => dashboards,
                currentDashboard: () => $scope.dashboard
            }
		});
        modal.result.then(function(data) {
            if (data) {
                $scope.dashboards[data.dashboardId] = data;
                selectDashboard(data);
            }
        });
	};

    $scope.deleteDashboard = function(dashboardId) {
        Dashboard.deleteDashboard(dashboardId).then(function() {
            delete $scope.dashboards[dashboardId];
            selectDashboard(lodash.first($scope.dashboards));
        });
    };

    $scope.saveDashboard = function(dashboard) {
        Dashboard.saveDashboard(dashboard).then(function(data) {
            selectDashboard(data.data);
        });
    };

	/**
     * @param {Number} dashboardId
     * @param {Number} widgetId
	 */
	$scope.deleteWidget = function(dashboardId, widgetId) {
		Dashboard.deleteWidget(dashboardId, widgetId).then(function(data) {
            selectDashboard(data.data);
		});

		return false;
	};

	$scope.editWidget = function(dashboardId, widget) {
        var modal = $uibModal.open({
            templateUrl: '/templates/widgets/edit.html',
            controller: 'EditWidgetController',
            resolve: {
                widget:      () => widget,
                dashboardId: () => dashboardId
            }
        });
        modal.result.then(function(data) {
            selectDashboard(data, true);
        });
	};
});
