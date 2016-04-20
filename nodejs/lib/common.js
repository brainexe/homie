
var config = require('./config');

module.exports.changeUser = function () {
    process.setuid(config['server.user']);
};

module.exports.requireRoot = function () {
    var isRoot = process.getuid && process.getuid() === 0;

    if (!isRoot) {
        console.error("You should execute this script as root!");
        process.exit();
    }
};
