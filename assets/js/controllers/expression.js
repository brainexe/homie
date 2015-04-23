
App.ng.controller('ExpressionController', ['$scope', function ($scope) {
	$scope.input_control  = [];
	$scope.expressions    = {};
    $scope.editExpression = {actions:['']};
    $scope.eventNames     = [];
    $scope.timers         = [];

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
        $scope.suggestionsActions = [].concat(
            $scope.input_control.map(function(regexp) {
                return 'input("' + regexp.substr(2, regexp.length - 4) + '")';
            }),
            Object.keys($scope.timers).map(function(key) {
                var timer = $scope.timers[key];
                return 'isTiming("' + timer.event.event.timingId + '")';
            })
        );
    };

    $scope.autocompleteCondition = function(typed) {
        $scope.suggestionsCondion = [].concat(
            $scope.eventNames.map(function(eventname) {
                return 'eventName == "' + eventname + '"';
            }),
            Object.keys($scope.timers).map(function(key) {
                var timer = $scope.timers[key];
                return 'isTiming("' + timer.event.event.timingId + '")';
            })
        );
    };

    $.get('/expressions/', function(data) {
		$scope.expressions   = data.expressions;
		$scope.input_control = data.input_control;
		$scope.eventNames    = data.events;
		$scope.timers        = data.timers;
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
        $scope.editExpression.actions.splice(index, 1);
    };

    $scope.addAction = function(expression) {
        expression.actions = expression.actions || [];
        if (expression.actions.indexOf('') == -1) {
            expression.actions.push('');
        }
    };

    $scope.addTimer = function(timer) {
        $.post('/expressions/timer/', timer, function(data) {
            $scope.timers = data.timers;
            $scope.$apply();
        });
    }

}]);
