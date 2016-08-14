
App.service('Prompt', /*@ngInject*/ function ($q) {
    return function (text) {
        return $q(function (resolve) {
            var value = prompt(text);
            resolve(value);
        });
    };
});
