
App.ng.controller('ExpressionController', ['$scope', function ($scope) {
	$scope.action         = [];
	$scope.expressions    = {};
    $scope.editExpression = {};
    $scope.eventNames     = [];

    var variables = [
        'event',
        'eventName'
    ];

    var functions = [
        'sprintf'
    ];

    $scope.suggestionsCondion = [];
    $scope.suggestionsActions = ["1", "2"];

    $scope.autocompleteAction = function(typed) {
        console.log(typed)
        $scope.suggestionsActions = $scope.actions.map(function(regexp) {
            //return "1sdf dsdsfnn" + ~~(Math.random() *10000);
            return '"' + regexp + '"';
        });
        console.log($scope.suggestionsActions)
    };

    $scope.autocompleteCondition = function(typed){
        $scope.suggestionsCondion = $scope.eventNames.map(function(eventname) {
            return 'eventName == "' + eventname + '"';
        });
    };

    $.get('/expressions/', function(data) {
		$scope.expressions = data.expressions;
		$scope.actions     = data.actions;
		$scope.eventNames  = data.events;
		$scope.$apply();
	});

    $scope.save = function(expression) {
        $.post('/expressions/save/', expression, function(data) {
            $scope.expressions[data.expressionId] = data;
            console.log(data);
            $scope.$apply();
        });
    };

    $scope.edit = function(expression) {
        $scope.editExpression = expression;
    };

    $scope.addAction = function(expression) {
        expression.actions = expression.actions || [];
        if (expression.actions.indexOf('') == -1) {
            expression.actions.push('');
        }
    };

}]);
