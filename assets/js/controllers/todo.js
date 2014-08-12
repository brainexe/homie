
// TODO eta
// TODO stroke
// TODO delete
App.ng.controller('TodoController', ['$scope', function($scope) {
	$scope.todos = [];
	$scope.shopping_list = [];
	$scope.user_names = [];

	$scope.stati = {
		"pending" : {id:'pending', name: "Pending", tasks: []},
		"progress" : {id:'progress', name: "Progress", tasks: []},
		"completed" : {id:'completed', name: "Completed", tasks: []}
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

	$scope.onDrop = function(status, event, data, tasks){
		// TODO skip if no change
		tasks.push(data);
		data.status = status;
		$.post('/todo/edit/', {
			id: data.id,
			changes: {
				status: status
			}
		});
	};
}]);

//$(".datetimepicker").datetimepicker({
//	showMinute: false
//});
//
//return;
//
//// Adding drop function to each category of task
//$.each(stati, function (index, value) {
//	$(value).droppable({
//		drop: function (event, ui) {
//
//			// Hiding Delete Area
//			$("#" + options.deleteDiv).hide();
//		}
//	});
//});
//
//// Adding drop function to delete div
//$("#" + options.deleteDiv).droppable({
//	drop: function(event, ui) {
//		var element = ui.helper,
//			css_id = element.attr("id"),
//			id = css_id.replace(options.taskId, ""),
//			object = todos[id];
//
//		// Removing old element
//		removeElement(object);
//
//		$.post('/todo/delete/', {'id':id});
//
//		delete todos[id];
//
//		// Hiding Delete Area
//		$("#" + options.deleteDiv).hide();
//	}
//});
//};