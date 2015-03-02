"use strict";

/**
 * @param {String} filename
 * @returns {String}
 */
function asset(filename) {
	return filename;
}

/**
 * @param {String} string
 * @returns {String}
 */
function _(string) {
	return string;
}

/**
 * @returns String {string}
 */
String.prototype.format = function () {
	var args = arguments;
	return this.replace(/{(\d+)}/g, function (match, number) {
		return typeof args[number] != 'undefined'
			? args[number]
			: match
			;
	});
};

var App = {
	ng: angular.module('raspberry', [
		'ngDragDrop',
		'ui.bootstrap',
		'yaru22.angular-timeago',
		'ngRoute'
	]),

	init: function (debug, userVo, socketUrl) {
		App.Layout.init(debug, userVo);
		if (socketUrl) {
			App.SocketServer.connect(socketUrl);
		}
	}
};

require.config({
	paths: {
		'mood': asset('mood.js').replace('.js', ''),
		'sensor': asset('sensor.js').replace('.js', '')
	}
});

$(document).ajaxComplete(function (event, request) {
	var flash = request.getResponseHeader('X-Flash');
	if (flash) {
		flash = JSON.parse(flash);
		App.Layout.$scope.addFlash(flash[1], flash[0]);
	}
});

$(function () {
	//Enable sidebar toggle
    $('.sidebar-menu a').click(function() {
        if ($(window).width() <= 992) {
            toggle()
        }
    });
	var toggle = function (e) {
		e && e.preventDefault();

		//If window is small enough, enable sidebar push menu
		if ($(window).width() <= 992) {
			var row_offcanvas = $('.row-offcanvas');
			row_offcanvas.toggleClass('active');
			$('.left-side').removeClass("collapse-left");
			$(".right-side").removeClass("strech");
			row_offcanvas.toggleClass("relative");
		} else {
			//Else, enable content streching
			$('.left-side').toggleClass("collapse-left");
			$(".right-side").toggleClass("strech");
		}
	};

    document.getElementById('offcanvas').onclick = toggle;

	/*
	 * Make sure that the sidebar is streched full height
	 * ---------------------------------------------
	 * We are gonna assign a min-height value every time the
	 * wrapper gets resized and upon page load. We will use
	 * Ben Alman's method for detecting the resize event.
	 **/
	function _fix() {
		//Get window height and the wrapper height
		var height = $(window).height() - $("body > .header").height();
		var wrapper = document.getElementById('wrapper');
		wrapper.style.minHeight = height + "px";

		var content = $(wrapper).height();
		$(".left-side, html, body").css("min-height", Math.min(height, content) + "px");
	}

	//Fire upon load
	_fix();
	//Fire when wrapper is resized
	window.onresize = function () {
		_fix();
	};
});

window['init'] = function (debug, user_vo, socket_server) {
	App.init(debug, user_vo, socket_server);
};
