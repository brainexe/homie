
App.service('Widget.todo_list', /*@ngInject*/ function(Todo, _, lodash) {
    return {
        template: '/templates/widgets/todo_list.html',
        render ($scope) {
            $scope.newTitle = '';
            $scope.items    = [];

            // todo put hidden states into widget settings

            Todo.getData().then(function (data) {
                $scope.items  = data.data.list;
                $scope.states = data.data.states;
            });

            $scope.filterStatus = (item, status) => item.status === status;

            $scope.addTodo = function(name) {
                if (!name) {
                    return;
                }
                Todo.add({name}).then(function(newItem) {
                    $scope.items.push(newItem.data);
                    $scope.newTitle = '';
                });
            };

            $scope.setStatus = function (item, status) {
                if (status === 'delete') {
                    Todo.deleteItem(item.todoId);
                    lodash.pullAllBy($scope.items, [{todoId: item.todoId}], 'todoId');
                } else {
                    item.status = status;
                    Todo.edit(item);
                }
            };
        }
    };
});
