
App.controller('AdminNodesController', /*@ngInject*/ function ($scope, Nodes) {
    $scope.nodes      = [];
    $scope.currentId  = [];
    $scope.types      = [];
    $scope.newNode    = {};

    Nodes.getData().success(function(data) {
        $scope.nodes      = data.nodes;
        $scope.currentId  = data.currentId;
        $scope.types      = data.types;
    });

    $scope.addNode = function(node) {
        Nodes.add(node).success(function(newNode) {
            $scope.nodes.push(newNode);
        });
    };

    $scope.edit = function($index, node) {
        Nodes.edit(node).success(function(newNode) {
            $scope.nodes[$index] = newNode;
            $scope.newNode = {};
        });
    };

    $scope.remove = function($index, node) {
        Nodes.remove(node).success(function() {
            $scope.nodes.splice($index, 1);
        });
    };
});
