App.Layout = {};

App.ng.controller('LayoutController', ['$scope', 'gettextCatalog', function ($scope, gettextCatalog) {
    App.Layout.$scope = $scope;

    $scope.flash_bag = [];
    $scope.languages = {
        'DE': 'Deutsch',
        'EN': 'English'
    };

    $scope.current_user = App.user;

    $scope.changeLanguage = function(lang) {
        gettextCatalog.setCurrentLanguage(lang);
    };

    /**
     * @returns {Boolean}
     */
    $scope.isLoggedIn = function () {
        return $scope.current_user && $scope.current_user.id > 0;
    };

    $scope.removeFlash = function(index) {
        $scope.flash_bag.splice(index, 1);
    };

    /**
     * @param {String} type
     * @param {String} message
     */
    $scope.addFlash = function (message, type) {
        type = type || 'success';

        var item = {
            type: type,
            message: message
        };

        $scope.flash_bag.push(item);

        window.setTimeout(function () {
            var index = $scope.flash_bag.indexOf(item);

            if (index > -1) {
                $scope.flash_bag.splice(index, 1);
                $scope.$apply();
            }
        }, 5000);

        $scope.$apply();
    };

    $scope.search = function(query) {
        $
            .get('/search/', {query: query})
            .then(function (data) {
                console.log(data);
            });
    };

    $scope.$on('sensor.value', function (event_name, event) {
        var text = '{0}: {1}'.format(event.sensorVo.name, event.valueFormatted);
        App.Notification.show(text);
    });
}]);

