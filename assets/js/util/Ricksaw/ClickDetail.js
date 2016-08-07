
// source http://stackoverflow.com/questions/10490041/onclick-option-for-rickshaw-charting
Rickshaw.namespace('Rickshaw.Graph.ClickDetail');
Rickshaw.Graph.ClickDetail = Rickshaw.Class.create({
    initialize: function (args) {
        this.graph = args.graph;
        this.clickHandler = args.clickHandler || function(data){};
        this._lastEvent = null;
        this._addListeners();
    },

    update: function (e) {
        e = e || this._lastEvent;
        if (!e) return;
        this._lastEvent = e;

        if (!e.target.nodeName.match(/^(path|svg|rect|circle)$/)) return;

        const eventX = e.offsetX || e.layerX;
        const eventY = e.offsetY || e.layerY;

        var graph = this.graph;
        var j = 0;
        var nearestPoint;
        var nearestValue;

        this.graph.series.active().forEach(function (series) {
            const data = this.graph.stackedData[j++];
            const domainX = graph.x.invert(eventX);

            if (!data.length) {
                return;
            }

            var domainIndexScale = d3.scale.linear()
                .domain([data[0].x, data.slice(-1)[0].x])
                .range([0, data.length - 1]);

            var approximateIndex = Math.round(domainIndexScale(domainX));
            if (approximateIndex == data.length - 1) approximateIndex--;

            var dataIndex = Math.min(approximateIndex || 0, data.length - 1);

            for (var i = approximateIndex; i < data.length - 1;) {
                if (!data[i] || !data[i + 1]) break;

                if (data[i].x <= domainX && data[i + 1].x > domainX) {
                    dataIndex = Math.abs(domainX - data[i].x) < Math.abs(domainX - data[i + 1].x) ? i : i + 1;
                    break;
                }

                if (data[i + 1].x <= domainX) {
                    i++
                } else {
                    i--
                }
            }

            if (dataIndex < 0) dataIndex = 0;
            var value = data[dataIndex];
            var distance = Math.sqrt(
                Math.pow(Math.abs(graph.x(value.x) - eventX), 2) +
                Math.pow(Math.abs(graph.y(value.y + value.y0) - eventY), 2)
            );

            if (!nearestPoint || distance < nearestPoint.distance) {
                value.series = series;
                nearestValue = value;
            }
        }, this);
        if (nearestValue){
            this.clickHandler(nearestValue);
        }
    },

    render: function (args) {
        // Do nothing
    },

    _addListeners: function () {
        this.graph.element.addEventListener(
            'click',
            function (e) {
                this.update(e);
            }.bind(this),
            false
        );
    }
});
