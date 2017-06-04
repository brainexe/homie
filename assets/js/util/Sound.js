
App.service('Sound', [function () {
    return {
        play: function (file) {
            console.debug("play sound" + file);
            let audio = new Audio(file);
            audio.play();
        }
    }
}]);
