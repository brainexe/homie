
// TODO stroke
App.ng.controller('TodoController', ['$scope', function($scope) {
	$scope.todos = [];
	$scope.shoppingList = [];
	$scope.userNames = [];

	$scope.stati = {
		"pending"   : {id:'pending', name: "Pending", tasks: [], prio:1},
		"progress"  : {id:'progress', name: "Progress", tasks: [], prio:2},
		"completed" : {id:'completed', name: "Completed", tasks: [], prio:3}
	};

	$.get('/todo/', function(data) {
		$scope.shoppingList = data.shoppingList.map(function(text) {
			return {text:text, done:false};
		});

		for (var userId in data.userNames) {
			$scope.userNames.push({id: userId, name: data.userNames[userId]})
		}

		// todo native loop
		$.each(data.list, function(index, params) {
			$scope.stati[params.status].tasks.push(params);
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

	$scope.assign = function(item_id, userId) {
		$.post('/todo/assign/', {
			id: item_id,
			userId: userId
		});
	};

	$scope.addTodo = function() {
		var errorMessage = _("name can not be empty"),
			name, description, date, tempData;

		name = $scope.newTitle;
		description = $scope.newDescription;
		date = $scope.newDateline;

		if (!name) {
			alert(errorMessage);
			return;
		}

		tempData = {
			name: name,
			deadline: date,
			description: description
		};

		$.post('/todo/add/', tempData, function(result) {
			$scope.stati['pending'].tasks.push(result);
			$scope.newTitle = $scope.newDescription = $scope.newDateline = '';
			$scope.$apply();
		});
	};

	$scope.dropSuccessHandler = function(index, status, todo_id, array) {
		array.splice(index, 1);
	};

	$scope.onDelete = function(data) {
		$.post('/todo/delete/', {'id':data.todoId});
	};

	$scope.onDrop = function(status, event, data, tasks) {
		tasks.push(data);

		if (status == data.status) {
			return;
		}

		data.status = status;
		$.post('/todo/edit/', {
			id: data.todoId,
			changes: {
				status: status
			}
		});
	};
	//TODO add shopping list entry is gone?
}]);
