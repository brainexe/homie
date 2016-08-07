
App.service('Widget.todo_list', /*@ngInject*/ function(Todo, _, lodash) {
    return {
        template: '/templates/widgets/todo_list.html',
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
                    lodash.pullAllBy($scope.items, [{'todoId': item.todoId}], 'todoId');
                } else {
                    item.status = status;
                    Todo.edit(item);
                }
            };
        }
    };
});
