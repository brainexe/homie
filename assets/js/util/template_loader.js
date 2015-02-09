
App.TemplateLoader = function(directory) {
	this.prefix     = directory || '';
	this._templates = {};
	this._loading   = {};
	this._queues    = {};
};

App.TemplateLoader.prototype.load = function(type) {
	var self = this;
	return new Promise(function(resolve, reject) {
		if (self._templates[type]) {
			resolve(type);
		}

		if (self._loading[type]) {
			var queue = self._queues[type] = self._queues[type] || [];
			queue.push(resolve);
			return;
		}

		self._loading[type] = true;

		$.get('/templates/' + self.prefix + type + '.html', function(template) {
			delete self._loading[type];
			self._templates[type] = template;
			resolve(template);

			if (self._queues[type]) {
				self._queues[type].forEach(function(resolve) {
					resolve(template);
				})
			}
			delete(self._queues[type]);
		});
	});
};
