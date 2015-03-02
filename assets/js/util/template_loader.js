
App.TemplateLoader = function() {
	this._templates = {};
	this._loading   = {};
	this._queues    = {};
};

App.TemplateLoader.prototype.load = function(file) {
	var self = this;

	return new Promise(function(resolve, reject) {
		if (self._templates[file]) {
			resolve(file);
		}

		if (self._loading[file]) {
			var queue = self._queues[file] = self._queues[file] || [];
			queue.push(resolve);
			return;
		}

		self._loading[file] = true;

		$.get(file, function(template) {
			delete self._loading[file];
			self._templates[file] = template;
			resolve(template);

			if (self._queues[file]) {
				self._queues[file].forEach(function(resolve) {
					resolve(template);
				})
			}
			delete(self._queues[file]);
		});
	});
};
