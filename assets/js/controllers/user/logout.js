
App.ng.controller('LogoutController', ['$scope', function($scope) {
	$.post('/logout/', function(user_vo) {
		App.Layout.$scope.current_user = user_vo;
		App.Layout.$scope.$apply();

		window.location.href = '#index';
	})
}]);
