
App.ng.controller('ShoppingListController', ['$scope', function($scope) {
	$scope.shoppingList = [];

	$.get('/todo/', function(data) {
		$scope.shoppingList = data.shoppingList.map(function(text) {
			return {text:text, done:false};
		});

		$scope.$apply();
	});

	$scope.addShoppingItem = function() {
		var name = $scope.todoText;

		if (!name) {
			return;
		}

		$.post('/todo/shopping/add/', {name: name});

		$scope.shoppingList.push({text: name, done:false});
		$scope.todoText = '';
	};

	$scope.change = function(name, done) {
		if (done) {
			$.post('/todo/shopping/remove/', {name:name});
		} else {
			$.post('/todo/shopping/add/', {name: name});
		}
	};
}]);
