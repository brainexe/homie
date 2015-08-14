App.controller('TodoController', ['$scope', '_', 'Todo', 'UserManagement', function ($scope, _, Todo, UserManagement) {
    $scope.todos     = [];
    $scope.userNames = [];

    // todo from BE + cache + state maschine
    $scope.stati = {
        "open":      {id: 'open', name: _("Open"), tasks: []},
        "pending":   {id: 'pending', name: _("Pending"), tasks: []},
        "progress":  {id: 'progress', name: _("Progress"), tasks: []},
        "completed": {id: 'completed', name: _("Completed"), tasks: []}
    };

    UserManagement.list().success(function (userNames) {
        for (var userId in userNames) {
            $scope.userNames.push({
                id: userId, name: userNames[userId]
            });
        }
    });

    Todo.getData().success(function (data) {
        for (var id in data.list) {
            var item = data.list[id];
            $scope.stati[item.status].tasks.push(item);
        }
    });

    $scope.assign = function (itemId, userId) {
        Todo.assign(itemId, userId);
    };

    $scope.addTodo = function () {
        var errorMessage = _("Name must not be empty"),
            name, description, date, tempData;

        description = $scope.newDescription;
        name = $scope.newTitle;
        date = $scope.newDateline;

        if (!name) {
            alert(errorMessage);
            return;
        }

        tempData = {
            name: name,
            deadline: date,
            description: description,
            status: 'open'
        };

        Todo.add(tempData).success(function (result) {
            $scope.stati[result.status].tasks.push(result);
            $scope.newTitle = $scope.newDescription = $scope.newDateline = '';
        });
    };

    $scope.dropSuccessHandler = function (index, status, todo_id, array) {
        array.splice(index, 1);
    };

    $scope.onDelete = function (data) {
        Todo.deleteItem(data.todoId);
    };

    $scope.onDrop = function (status, event, data, tasks) {
        tasks.push(data);

        if (status == data.status) {
            return;
        }

        data.status = status;
        Todo.edit(data);
    };
}]);
