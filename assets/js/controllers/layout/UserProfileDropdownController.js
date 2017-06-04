
App.controller('UserProfileDropdownController', /*@ngInject*/ function ($scope, Config) {
    $scope.locales = [];

    Config.getAll().then(function(config) {
        $scope.locales = config.locales;
    });
});

