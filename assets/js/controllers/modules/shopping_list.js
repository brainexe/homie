
App.controller('ShoppingListController', /*@ngInject*/ function ($scope, ShoppingList) {
    $scope.shoppingList = [];
    $scope.itemText = '';

    ShoppingList.getData().success(function (data) {
        $scope.shoppingList = data.shoppingList.map(text => ({text, done: false}));
    });

    $scope.addShoppingItem = function () {
        var text = $scope.itemText;
        if (!text) {
            return;
        }

        ShoppingList.add(text);

        $scope.shoppingList.push({text, done: false});
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
