
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

	var etas = $(".eta");
	if (etas.length) {
		etas.prettyDate(1);
	}
});