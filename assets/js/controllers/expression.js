
App.ng.controller('ExpressionController', ['$scope', function ($scope) {
	$scope.action         = [];
	$scope.expressions    = {};
    $scope.editExpression = {actions:['']};
    $scope.eventNames     = [];

    var variables = [
        'event',
        'eventName'
    ];

    var actions = [
        'input("say foo")',
        'setProperty("test", eventNae)'
    ];

    $scope.suggestionsCondion = [];
    $scope.suggestionsActions = [];

    $scope.autocompleteAction = function(typed) {
        $scope.suggestionsActions = $scope.actions.map(function(regexp) {
            return '"' + regexp.substr(2, regexp.length - 4) + '"';
        });
        $scope.suggestionsActions = $scope.suggestionsActions.concat(actions);
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
            $scope.$apply();
        });
    };

    $scope.delete = function(expressionId) {
        $.post('/expressions/delete/', {expressionId:expressionId}, function() {
            delete $scope.expressions[expressionId];
            $scope.$apply();
        });
    };

    $scope.edit = function(expression) {
        $scope.editExpression = expression;
    };

    $scope.deleteAction = function(index) {
        console.log($scope.editExpression.actions);
        $scope.editExpression.actions.splice(index, 1);
        console.log(index)
        console.log($scope.editExpression.actions)
    };

    $scope.addAction = function(expression) {
        expression.actions = expression.actions || [];
        if (expression.actions.indexOf('') == -1) {
            expression.actions.push('');
        }
    };

}]);
