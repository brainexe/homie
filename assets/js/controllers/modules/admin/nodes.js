
App.controller('AdminNodesController', /*@ngInject*/ function ($scope, Nodes, OrderByMixin, lodash) {
    angular.extend($scope, OrderByMixin);

    $scope.nodes      = {};
    $scope.currentId  = [];
    $scope.types      = [];
    $scope.newNode    = {};
    $scope.orderBy    = 'name';

    Nodes.getData().then(function(data) {
        $scope.nodes      = lodash.keyBy(data.data.nodes, 'nodeId');
        $scope.currentId  = data.data.currentId;
        $scope.types      = data.data.types;
    });

    $scope.addNode = function(node) {
        Nodes.add(node).then(function(newNode) {
            $scope.nodes[newNode.nodeId] = newNode.data;
        });
    };

    $scope.edit = function(node) {
        Nodes.edit(node).then(function(newNode) {
            $scope.nodes[newNode.data.nodeId] = newNode.data;
            $scope.newNode = {};
        });
    };

    $scope.removeOption = function(options, key) {
        delete options[key];
    };

    $scope.addOption = function(node) {
        var key   = prompt("Key");
        var value = prompt("Value");

        if (Array.isArray(node.options)) {
            node.options = {};
        }

        node.options[key] = value;
    };

    $scope.editOption = function(options, key) {
        var value = prompt("Value");
        options[key] = value;
    };

    $scope.remove = function(node) {
        Nodes.remove(node).then(function() {
            delete $scope.nodes[node.nodeId];
        });
    };
});
