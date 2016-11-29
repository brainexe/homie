
App.directive('qrCode', function () {
    return {
        restrict: 'E',
        replace: true,
        template: '<img ng-src="https://api.qrserver.com/v1/create-qr-code/?size={{size}}x{{size}}&data={{formattedData}}"/>',

        link ($scope) {
            $scope.size          = $scope.size || 250;
            $scope.formattedData = encodeURIComponent($scope.data);
        },
        scope: {
            data: "=",
            size: "&"
        }
    };
});
