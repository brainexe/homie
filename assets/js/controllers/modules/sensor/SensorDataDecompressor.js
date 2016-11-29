
App.service('SensorDataDecompressor', /*@ngInject*/ function() {
    return function decompressData(data) {
        let final = [];
        for (let sensorId in data.json) {
            let graphData = [];
            let length = data.json[sensorId].data.length;
            for (let i = 0; i < length; i += 2) {
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
