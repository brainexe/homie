
App.service('Widget.command', /*@ngInject*/ function(Command) {
    return {
        template: '/templates/widgets/command.html',
        render: function ($scope, widget) {
            $scope.value = '';

            $scope.execute = function(value) {
                Command.execute(value).success(function(output) {
                    $scope.output = output;
                });

                $scope.value = '';
            }
        }
    };
});

