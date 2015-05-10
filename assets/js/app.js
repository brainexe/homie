"use strict";

/**
 * Pseudo function needed to mark asset links. during console assets:dump the links are replaced by hashed ones
 * @param {String} filename
 * @returns {String}
 */
function asset(filename) {
	return filename;
}

var gettext = gettext || function(s) {
	return s
};

/**
 * @returns String {string}
 */
String.prototype.format = function () {
	var args = arguments;
	return this.replace(/{(\d+)}/g, function (match, number) {
		return typeof args[number] != 'undefined'
			? args[number]
			: match;
	});
};

var App = {
    debug: null, // TODO
    user: null,

	ng: angular.module('homie', [
		'ngDragDrop',
		'autocomplete',
        'ngRoute',
		'ui.bootstrap',
		'ui.select',
		'yaru22.angular-timeago',
        'gettext',
		'ngSanitize'
	]).config(['$routeProvider', function ($routeProvider) {
        for (var i in controllers) {
            var metadata = controllers[i];
            $routeProvider.when('/' + metadata.url, metadata);
        }

        $routeProvider.otherwise({redirectTo: '/index'});
    }]).run(function(gettextCatalog) {
        gettextCatalog.setCurrentLanguage('DE');
        gettextCatalog.debug = true;
        gettextCatalog.debugPrefix = '?';
    }),

	init: function (debug, socketUrl) {
        App.debug  = debug;

		if (socketUrl) {
			App.SocketServer.connect(socketUrl);
		}
	}
};

require.config({
	paths: {
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
	var toggle = function (e) {
		e && e.preventDefault();

		//If window is small enough, enable sidebar push menu
		if ($(window).width() <= 992) {
			var rowOffcanvas = $('.row-offcanvas');
			rowOffcanvas.toggleClass('active');
			$('.left-side').removeClass("collapse-left");
			$(".right-side").removeClass("strech");
			rowOffcanvas.toggleClass("relative");
		} else {
			//Else, enable content streching
			$('.left-side').toggleClass("collapse-left");
			$(".right-side").toggleClass("strech");
		}
	};
    $('.sidebar-menu a').click(function() {
        if ($(window).width() <= 992) {
            toggle()
        }
    });
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

window['init'] = function (debug, user, socketUrl) {
	App.init(debug, user, socketUrl);
};
