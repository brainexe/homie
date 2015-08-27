
App.directive('expressionParameter', [function() {

    function stringify(parts) {
        if (!parts[0]) {
            return '';
        }

        return parts[0] + '(' + parts[1].join(', ') +')';
    }

    function parse(parameterString) {
        var args = /(\w+?)\(\s*([^)]+?\s*)\s*\)/.exec(parameterString);
        if (!args) {
            return [null, []];
        }
        var functionName = args[1];


        var parameterList = args[2].split(',').map(function(param) {
            return param.trim();
        });

        return [
            functionName,
            parameterList
        ];
    }

    var conditions = {
        'isCron': [['10Minutes', '20Minutes']]
    };

    var actions = {
    };

    function link(scope, element, attrs) {
        var parameter = scope.parameter;

        scope.functions = scope.type == 'conditions' ? conditions : actions;
        scope.parts = parse(parameter);
    }

    return {
        templateUrl: '/templates/expression/parameter.html',
        restrict: "E",
        link: link,
        scope: {
            parameter: '=',
            type: '='
        }
    };
}]);