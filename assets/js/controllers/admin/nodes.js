
App.controller('AdminNodesController', ['$scope', 'Nodes', function ($scope, Nodes) {
    $scope.nodes      = {};
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
            $scope.nodes[newNode.nodeId] = newNode
        });
    };

    $scope.edit = function(node) {
        Nodes.edit(node).success(function(newNode) {
            $scope.nodes[newNode.nodeId] = newNode;
            $scope.newNode = {};
        });
    };

    $scope.remove = function(node) {
        Nodes.remove(node).success(function() {
            delete $scope.nodes[node.nodeId];
        });
    }
}]);
