
App.controller('ShoppingListController', ['$scope', 'ShoppingList', function ($scope, ShoppingList) {
    $scope.shoppingList = [];

    ShoppingList.getData().success(function (data) {
        $scope.shoppingList = data.shoppingList.map(function (text) {
            return {text: text, done: false};
        });
    });

    $scope.addShoppingItem = function () {
        var name = $scope.todoText;

        if (!name) {
            return;
        }

        ShoppingList.add({name: name});

        $scope.shoppingList.push({text: name, done: false});
        $scope.todoText = '';
    };

    $scope.change = function (item) {
        if (item.done) {
            ShoppingList.remove(item.text);
        } else {
            ShoppingList.add(item.text);
        }
    };
}]);
