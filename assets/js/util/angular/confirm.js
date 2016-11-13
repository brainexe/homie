/**
 * @source https://github.com/venil7/Angular-ui-confirm
 */
App.directive('confirm', /*@ngInject*/ function($uibModal, $document) {
    return {
        restrict: 'A',
        scope: {
            confirm: '&confirm',
            confirmHeader: '@',
            confirmText:   '@'
        },
        link (scope, elem) {
            elem.on('click', function() {
                var modalInstance = $uibModal.open({
                    templateUrl: '/templates/modal/confirm.html',
                    controller: ["$scope", "$uibModalInstance", "text", "header", "_", function($scope, $uibModalInstance, text, header, _) {
                        $scope.message = text   || _('Confirm');
                        $scope.header  = header || _('Confirm');
                        $scope.ok      = () => $uibModalInstance.close(true);
                        $scope.cancel  = () => $uibModalInstance.dismiss('cancel');

                        $document.bind('keydown', function(evt) {
                            if (evt.isDefaultPrevented()) {
                                return evt;
                            }
                            if (evt.which === 13){
                                $uibModalInstance.close(true);
                            }
                        });
                    }],
                    resolve: {
                        text:   () => scope.confirmText,
                        header: () => scope.confirmHeader
                    }
                });


                modalInstance.result.then(function () {
                    scope.confirm();
                });
            });
        }
    };
});
