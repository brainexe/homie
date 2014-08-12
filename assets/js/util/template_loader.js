
App.TemplateLoader = function(directory) {
	this.prefix = directory || '';
	this._templates = {};
	this._loading = {};
	this._queues = {};
};

App.TemplateLoader.prototype.load = function(type, callback) {
	callback = callback || function(){};

	if (this._templates[type]) {
		callback(type);
	}

	if (this._loading[type]) {
		var queue = this._queues[type] = this._queues[type] || [];
		queue.push(callback);
		return;
	}

	this._loading[type] = true;

	var self = this;
	$.get('/templates/' + this.prefix + type + '.html', function(template) {
		delete self._loading[type];
		self._templates[type] = template;
		callback(template);

		if (self._queues[type]) {
			self._queues[type].forEach(function(callback) {
				callback(template);
			})
		}
		delete(self._queues[type]);
	});
};