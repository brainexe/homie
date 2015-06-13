
App.controller('HelpModalController', function ($scope, $modalInstance, Help, type, content) {
    console.log(arguments);
    $scope.type    = type;
    $scope.content = content;

    $scope.saveHelp = function() {
        Help.save($scope.type, $scope.content);
    }
});

App.directive('help', ['$modal', 'Help', function ($modal, Help) {
    return {
        restrict: 'E',
        scope: {
            type: '='
        },
        template: '<span style="opacity: 0.9" class="glyphicon glyphicon-book"></span> <a class="cursor" ng-click="open(type)">{{"Help"|translate}}</a>',

        link: function ($scope, element, attrs) {
            $scope.type = attrs.type;
            $scope.open = function (type) {
                console.log(type);
                Help.getAll().success(function(data) {
                    $modal.open({
                        templateUrl: '/templates/modal/help.html',
                        controller:  'HelpModalController',
                        resolve: {
                            type: function () {
                                return type;
                            },
                            content: function() {
                                return data[type]
                            }
                        }
                    });
                });
            };
        }
    }
}]);
