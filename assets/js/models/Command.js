
App.service("Command", /*@ngInject*/ function($http) {
    return {
        execute: (command) =>
            $http.post("/command/", {
                command: command
            })

    };
});
