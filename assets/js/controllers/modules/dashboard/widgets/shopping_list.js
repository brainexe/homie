
App.service('Widget.shopping_list', /*@ngInject*/ function(ShoppingList) {
    return {
        template: '/templates/widgets/shopping_list.html',
        render ($scope) {
            $scope.$on('shopping_list.add', function(event, data) {
                $scope.shoppingList.push({text: data.item, done: false});
            });

            $scope.todoText = '';
            ShoppingList.getData().success(function (data) {
                $scope.shoppingList = data.shoppingList.map(function (text) {
                    return {text, done: false};
                });
            });

            $scope.addShoppingItem = function() {
                var name = $scope.todoText;
                if (!name) {
                    return;
                }

                ShoppingList.add(name);

                $scope.todoText = '';
            };

            $scope.change = function (item) {
                if (item.done) {
                    ShoppingList.remove(item.text);
                } else {
                    ShoppingList.add(item.text);
                }
            };
        }
    };
});

