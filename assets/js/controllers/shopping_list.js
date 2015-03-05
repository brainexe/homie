
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

	$scope.change = function(item) {
		if (item.done) {
			$.post('/todo/shopping/remove/', {name: item.text});
		} else {
			$.post('/todo/shopping/add/', {name: item.text});
		}
	};
}]);
