
App.service('Widget.time', /*@ngInject*/ function() {
    return {
        template: '/templates/widgets/time.html',
        render ($scope) {
            $scope.$on('secondTimer', function (event, nowTime) {
                $scope.time = nowTime;
            });
        }
    };
});
