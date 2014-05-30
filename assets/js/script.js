
// TODO replace by bootstrap/jquery
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

		App.emitter.on('espeak.speak', function(event) {
			App.showNotification(event.espeak.text);
		})
	},

	showNotification: function(content) {
		if (!("Notification" in window)) {
			return;
		} else if (Notification.permission === "granted") {
			// If it's okay let's create a notification
			var notification = new Notification(content);
		} else if (Notification.permission !== 'denied') {
			Notification.requestPermission(function (permission) {

				// Whatever the user answers, we make sure we store the information
				if(!('permission' in Notification)) {
					Notification.permission = permission;
				}

				// If the user is okay, let's create a notification
				if (permission === "granted") {
					var notification = new Notification(content);
				}
			});
		}
	}
};