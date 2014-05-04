
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

window.emitter = new EventEmitter2({
	'wildcard': true
});

emitter.on('*.*', function(event){
	console.log("socket server:", event.event_name, event)
});

$(function() {
	$('.tip').tooltip();

	var etas = $(".eta");
	if (etas.length) {
		etas.prettyDate(1);
	}

	var sockjs = new SockJS("http://localhost:8081/socket"); //TODO config
	sockjs.onmessage = function(message) {
		var event = JSON.parse(message.data);
		var event_name = event.event_name;
		window.emitter.emit(event_name, event);
	};
});