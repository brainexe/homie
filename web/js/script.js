

function togglePanel(element) {
	element.nextElementSibling.classList.toggle('hidden');
}

$(function() {
	$('.tip').tooltip();
});