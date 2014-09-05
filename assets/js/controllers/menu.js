
App.ng.controller('MenuController', ['$scope', '$route', function ($scope, $routeProvider) {
	$scope.$parent.$watch('current_user', function(value){
		console.log(value);
		var is_logged_in = $scope.$parent.isLoggedIn();

		$scope.menu = App.Layout.controllers.filter(function(item) {
			if (!item.name) {
				return false;
			}

			if (!is_logged_in && !item.is_public) {
				return false;
			} else if (is_logged_in && item.is_public === true) {
				return false;
			}

			return true;
		});
	});
}]);

