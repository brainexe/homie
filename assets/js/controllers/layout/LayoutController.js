
App.controller('LayoutController', /*@ngInject*/ function ($scope, $window, Cache) {
    $scope.currentUser = {};
    $scope.isLoggedIn  = false;

    $scope.$on('currentuser.update', (event, user) => {
        $scope.currentUser = user;
        $scope.isLoggedIn  = user && user.userId > 0;
    });

    $scope.flushCache = () => {
        Cache.destroy();
        $window.location.reload();
    };
});

