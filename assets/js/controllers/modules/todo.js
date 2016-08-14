App.controller('TodoController', /*@ngInject*/ function ($scope, _, lodash, Todo, UserManagement) {
    $scope.userNames   = [];
    $scope.currentItem = {};

    UserManagement.list().success(function (userNames) {
        // todo separate service UserList, which is cached
        for (var userId in userNames) {
            $scope.userNames.push({
                userId: userId, name: userNames[userId]
            });
        }
    });

    Todo.getData().success(function (data) {
        $scope.states = data.states;
        $scope.items  = data.list;
    });

    $scope.assign = function (itemId, userId) {
        Todo.assign(itemId, userId);
    };

    $scope.setCurrent = function(item) {
        $scope.currentItem = item;
    };

    $scope.submit = function (item) {
        if (!item.name) {
            alert(_("Name must not be empty"));
            return;
        }

        if (item.todoId) {
            Todo.edit(item).success(function () {
                $scope.currentItem = {};
            });
        } else {
            item.status = 'open';
            Todo.add(item).success(function (result) {
                $scope.items.push(result);
                $scope.currentItem = {};
            });
        }
    };

    $scope.onDelete = function (data) {
        Todo.deleteItem(data.todoId).success(function() {
            lodash.pullAllBy($scope.items, [{'todoId': data.todoId}], 'todoId');
        });
    };

    $scope.onDrop = function (status, event, data) {
        if (status === data.status) {
            return;
        }
        data.status = status;

        $scope.items.forEach(function(current) {
            if (data.todoId == current.todoId) {
                current.status = data.status;
            }
        });

        Todo.edit(data);
    };

    $scope.editTodo = Todo.edit;
});
