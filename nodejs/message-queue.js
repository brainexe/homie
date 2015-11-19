
var worker = require('./lib/message-queue/worker.js');

if (!isRoot()) {
    console.error("You need to execute this script as root!");
    process.exit();
}
worker.run();

function isRoot() {
    return process.getuid && process.getuid() === 0;
}
