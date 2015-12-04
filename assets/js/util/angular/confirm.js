/**
 * @source https://github.com/venil7/Angular-ui-confirm
 */
App.directive('confirm', ['$uibModal', function($uibModal) {
    return {
        restrict: 'A',
        scope: {
            confirm: '&confirm',
            confirmHeader: '@',
            confirmText:   '@'
        },
        link: function(scope, elem) {
            elem.on('click', function() {
                var modalInstance = $uibModal.open({
                    templateUrl: '/templates/modal/confirm.html',
                    controller: ["$scope", "$uibModalInstance", "text", "header", '_', function($scope, $uibModalInstance, text, header, _) {
                        $scope.message = text   || _('Confirm');
                        $scope.header  = header || _('Confirm');
                        $scope.ok = function () {
                            $uibModalInstance.close(true);
                        };
                        $scope.cancel = function () {
                            $uibModalInstance.dismiss('cancel');
                        };
                    }],
                    resolve: {
                        text: function() {
                            return scope.confirmText;
                        },
                        header: function() {
                            return scope.confirmHeader;
                        }
                    }
                });
                modalInstance.result.then(function () {
                    scope.confirm();
                });
            });
        }
    };
}]);
