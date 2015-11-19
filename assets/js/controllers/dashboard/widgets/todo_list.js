
App.service('Widget.todo_list', ['Todo', "_", function(Todo, _) {
    return {
        render: function ($scope, widget) {
            $scope.newTitle = '';
            $scope.items    = [];

            // todo put hidden states into widget settings

            Todo.getData().success(function (data) {
                $scope.items  = data.list;
                $scope.states = data.states;
            });

            $scope.filterStatus = function(item, status) {
                return item.status == status;
            };

            $scope.addTodo = function(name) {
                if (!name) {
                    return;
                }
                Todo.add({
                    name: name
                }).success(function(newItem) {
                    $scope.items.push(newItem);
                    $scope.newTitle = '';
                });
            };

            $scope.setStatus = function (item, status) {
                if (status == 'delete') {
                    Todo.deleteItem(item.todoId);
                    $scope.items.removeByValue(item);
                } else {
                    item.status = status;
                    Todo.edit(item);
                }
            };
        }
    };
}]);
