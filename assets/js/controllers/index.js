
App.ng.controller('IndexController', ['$scope', function ($scope) {
	console.log('index');
	// todo use $locator
	if (App.Layout.$scope.isLoggedIn()) {
		window.location.href = '#/dashboard';
	} else {
		window.location.href = '#/login';
	}
}]);
