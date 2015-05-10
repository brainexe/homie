App.Layout = {};

App.ng.controller('LayoutController', ['$scope', 'gettextCatalog', function ($scope, gettextCatalog) {
    App.Layout.$scope = $scope;

    $scope.flashBag = [];
    $scope.languages = {
        'DE': 'Deutsch',
        'EN': 'English'
    };

    $scope.currentUser = {};

    $.get('/user/current/').then(function(user){
        $scope.currentUser = user;
        $scope.$apply();
    });

    $scope.changeLanguage = function(lang) {
        gettextCatalog.setCurrentLanguage(lang);
    };

    /**
     * @returns {Boolean}
     */
    $scope.isLoggedIn = function () {
        return $scope.currentUser && $scope.currentUser.id > 0;
    };

    $scope.removeFlash = function(index) {
        $scope.flashBag.splice(index, 1);
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

        $scope.flashBag.push(item);

        window.setTimeout(function () {
            var index = $scope.flashBag.indexOf(item);

            if (index > -1) {
                $scope.flashBag.splice(index, 1);
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

    $scope.$on('sensor.value', function (eventName, event) {
        var text = '{0}: {1}'.format(event.sensorVo.name, event.valueFormatted);
        App.Notification.show(text);
    });
}]);

