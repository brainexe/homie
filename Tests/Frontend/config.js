
exports.config = {
    capabilities: {
        'browserName': 'chrome'
    },

    specs: [
        'spec/util/angular/*.js'
    ],
    framework: 'jasmine',

    jasmineNodeOpts: {
        isVerbose: true
    }
};
