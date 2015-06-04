App.controller('TodoController', ['$scope', '_', 'Todo', function ($scope, _, Todo) {
    $scope.todos     = [];
    $scope.userNames = [];

    $scope.stati = {
        "pending": {id: 'pending', name: _("Pending"), tasks: [], prio: 1},
        "progress": {id: 'progress', name: _("Progress"), tasks: [], prio: 2},
        "completed": {id: 'completed', name: _("Completed"), tasks: [], prio: 3}
    };

    Todo.getData().success(function (data) {
        for (var userId in data.userNames) {
            $scope.userNames.push({
                id: userId, name: data.userNames[userId]
            });
        }

        for (var id in data.list) {
            var item = data.list[id];
            $scope.stati[item.status].tasks.push(item);
        }
    });

    $scope.assign = function (itemId, userId) {
        Todo.assign(itemId, userId);
    };

    $scope.addTodo = function () {
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

        Todo.add(tempData).success(function (result) {
            $scope.stati['pending'].tasks.push(result);
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

        Todo.edit(data.todoId, data);
    };
}]);
