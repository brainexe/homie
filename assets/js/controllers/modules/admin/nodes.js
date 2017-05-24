
App.controller('AdminNodesController', /*@ngInject*/ function ($scope, Nodes, OrderByMixin) {
    angular.extend($scope, OrderByMixin);

    $scope.nodes      = [];
    $scope.currentId  = [];
    $scope.types      = [];
    $scope.newNode    = {};
    $scope.orderBy    = 'name';

    Nodes.getData().then(function(data) {
        $scope.nodes      = data.data.nodes;
        $scope.currentId  = data.data.currentId;
        $scope.types      = data.data.types;
    });

    $scope.addNode = function(node) {
        Nodes.add(node).then(function(newNode) {
            $scope.nodes.push(newNode.data);
        });
    };

    $scope.edit = function($index, node) {
        Nodes.edit(node).then(function(newNode) {
            $scope.nodes[$index] = newNode.data;
            $scope.newNode = {};
        });
    };
    $scope.removeOption = function(options, key) {
        delete options[key];
    };

    $scope.addOption = function(options) {
        var key   = prompt("Key");
        var value = prompt("Value");
        options[key] = value;
    };

    $scope.editOption = function(options, key) {
        var value = prompt("Value");
        options[key] = value;
    };

    $scope.remove = function($index, node) {
        Nodes.remove(node).then(function() {
            $scope.nodes.splice($index, 1);
        });
    };
});
