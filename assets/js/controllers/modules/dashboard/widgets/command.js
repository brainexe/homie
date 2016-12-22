
App.service('Widget.command', /*@ngInject*/ function(Command) {
    return {
        template: '/templates/widgets/command.html',
        render ($scope) {
            $scope.value = '';

            $scope.execute = function(value) {
                Command.execute(value).then(function(output) {
                    $scope.output = output.data;
                });

                $scope.value = '';
            };
        }
    };
});

