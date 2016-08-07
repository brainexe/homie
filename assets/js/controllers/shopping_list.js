
App.controller('ShoppingListController', /*@ngInject*/ function ($scope, ShoppingList) {
    $scope.shoppingList = [];
    $scope.itemText = '';

    ShoppingList.getData().success(function (data) {
        $scope.shoppingList = data.shoppingList.map(function (text) {
            return {text: text, done: false};
        });
    });

    $scope.addShoppingItem = function () {
        var name = $scope.itemText;
        if (!name) {
            return;
        }

        ShoppingList.add(name);

        $scope.shoppingList.push({text: name, done: false});
        $scope.itemText = '';
    };

    $scope.change = function (item) {
        if (item.done) {
            ShoppingList.remove(item.text);
        } else {
            ShoppingList.add(item.text);
        }
    };
});
