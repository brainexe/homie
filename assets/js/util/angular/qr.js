
App.directive('qrCode', [function () {
    return {
        restrict: 'E',
        link: function (scope, element, attrs) {
            var size = scope.size || 250;
            var data = scope.data;
            var domElement = element[0];
            var url = '<img src="https://api.qrserver.com/v1/create-qr-code/?size={0}x{1}&data={2}"/>'.format(size, size, encodeURIComponent(data));

            domElement.innerHTML = url;
        },
        scope: {
            data: "=",
            size: "="
        }
    };
}]);
