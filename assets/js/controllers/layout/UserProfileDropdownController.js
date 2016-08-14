
App.controller('UserProfileDropdownController', /*@ngInject*/ function ($scope, Config) {
    $scope.locales = [];

    Config.getAll().success(function(config) {
        $scope.locales = config.locales;
    });
});

