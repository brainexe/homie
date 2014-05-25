
// TODO replace by bootstrap
function togglePanel(element) {
	element.nextElementSibling.classList.toggle('hidden');
}

$.fn.prettyDate = function(interval) {
	interval = interval || 10;

	return this.each(function() {
		var el = $(this);
		var timestamp = el.data('timestamp');

		var func = function() {
			el.text(moment.utc(timestamp, 'X').fromNow());
		};

		func();
		setInterval(func, interval*1000);
	});
};

$(function() {
	$('.tip').tooltip();
	$( ".datetimepicker" ).datetimepicker();

	var etas = $(".eta");
	if (etas.length) {
		etas.prettyDate(1);
	}
});

var App = {
	debug: false,
	emitter: null,

	start: function() {
		this.emitter = new EventEmitter2({
			'wildcard': true
		});

		if (this.debug) {
			this.emitter.on('*.*', function(event){
				console.log("socket server:", event.event_name, event)
			});
		}
	},

	connectToSocketServer: function(socket_url) {
		var sockjs = new SockJS(socket_url);

		sockjs.onmessage = function(message) {
			var event = JSON.parse(message.data);
			var event_name = event.event_name;
			App.emitter.emit(event_name, event);
		};
	}
};