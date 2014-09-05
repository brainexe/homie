
// TODO stroke
// todo prio
App.ng.controller('TodoController', ['$scope', function($scope) {
	$scope.todos = [];
	$scope.shopping_list = [];
	$scope.user_names = [];

	$scope.stati = {
		"pending" : {id:'pending', name: "Pending", tasks: [], prio:1},
		"progress" : {id:'progress', name: "Progress", tasks: [], prio:2},
		"completed" : {id:'completed', name: "Completed", tasks: [], prio:3}
	};

	$.get('/todo/', function(data) {
		$scope.shopping_list = data.shopping_list.map(function(text) {
			return {text:text, done:false};
		});

		for (var user_id in data.user_names) {
			$scope.user_names.push({id: user_id, name: data.user_names[user_id]})
		}

		$.each(data.list, function(index, params) {
			$scope.stati[params.status].tasks.push(params);
		});

		$scope.$apply();
	});

	$scope.addTodo = function() {
		$.post('/todo/shopping/add/', {name: $scope.todoText});

		$scope.todos.push({text:$scope.todoText, done:false});
		$scope.todoText = '';
	};

	$scope.change = function(name, done) {
		if (done) {
			$.post('/todo/shopping/remove/', {name:name});
		} else {
			$.post('/todo/shopping/add/', {name: name});
		}
	};

	$scope.assign = function(item_id, user_id) {
		$.post('/todo/assign/', {
			id: item_id,
			user_id: user_id
		});
	};

	$scope.addTodo = function() {
		var errorMessage = "name can not be empty",
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

	$scope.onDelete = function(data){
		$.post('/todo/delete/', {'id':data.id});
	};

	$scope.onDrop = function(status, event, data, tasks) {
		tasks.push(data);

		if (status == data.status) {
			return;
		}

		data.status = status;
		$.post('/todo/edit/', {
			id: data.id,
			changes: {
				status: status
			}
		});
	};
	//TODO add shopping list entry is gone?
}]);
