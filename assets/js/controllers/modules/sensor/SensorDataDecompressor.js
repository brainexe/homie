
App.service('SensorDataDecompressor', /*@ngInject*/ function() {
    return function decompressData(data) {
        // todo extrat + use generator
        let final = [];
        for (let sensorId in data.json) {
            let graphData = [];
            for (let i = 0; i < data.json[sensorId].data.length; i += 2) {
                graphData.push({
                    x: data.json[sensorId].data[i],
                    y: data.json[sensorId].data[i + 1]
                });
            }
            data.json[sensorId].data = graphData;
            final.push(data.json[sensorId]);
        }

        return final;
    };
});
