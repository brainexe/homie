"use strict";

/**
 * @param {String} filename
 * @returns {String}
 */
function asset(filename) {
	return filename;
}

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

	init: function (debug, user_vo, socket_server) {
		App.Layout.init(debug, user_vo);
		if (socket_server) {
			App.connectToSocketServer(socket_server);
		}
	},

	/**
	 * @param {String} socket_url
	 */
	connectToSocketServer: function (socket_url) {
		var sockjs = new SockJS(socket_url);

		sockjs.onmessage = function (message) {
			var event      = JSON.parse(message.data);
			var event_name = event.event_name;

			App.Layout.$scope.$broadcast(event_name, event);

			if (App.Layout.debug) {
				console.log("socket server: " + event.event_name, event)
			}
		};
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
