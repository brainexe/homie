// TODO replace by bootstrap/jquery
function togglePanel(element) {
	element.nextElementSibling.classList.toggle('hidden');
}

$.fn.prettyDate = function (interval) {
	interval = interval || 10;

	return this.each(function () {
		var el = $(this);
		var timestamp = el.data('timestamp');

		var func = function () {
			el.text(moment.utc(timestamp, 'X').fromNow());
		};

		func();
		setInterval(func, interval * 1000);
	});
};

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
	debug: false,
	ng: angular.module('raspberry', [
		'ngDragDrop',
		'yaru22.angular-timeago',
		'ngRoute'
	]),

	/**
	 * @param {String} socket_url
	 */
	connectToSocketServer: function (socket_url) {
		var sockjs = new SockJS(socket_url);

		sockjs.onmessage = function (message) {
			var event = JSON.parse(message.data);
			var event_name = event.event_name;

			App.Layout.$scope.$broadcast(event_name, event);

			if (this.debug) {
				console.log("socket server:", event.event_name, event)
			}
		};
	},

	/**
	 * @param {String} content
	 */
	showNotification: function (content) {
		if (!("Notification" in window)) {
			return;
		} else if (Notification.permission === "granted") {
			// If it's okay let's create a notification
			var notification = new Notification(content);
			window.setTimeout(function () {
				notification.close();
			}, 10000);
		} else if (Notification.permission !== 'denied') {
			Notification.requestPermission(function (permission) {

				// Whatever the user answers, we make sure we store the information
				if (!('permission' in Notification)) {
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

App.Layout = {
	$scope: null,
	init: function (current_user) {
		App.ng.controller('LayoutController', ['$scope', function ($scope) {
			App.Layout.$scope = $scope;

			$scope.flash_bag = [];
			$scope.current_user = current_user;

			/**
			 * @returns {Boolean}
			 */
			$scope.isLoggedIn = function () {
				return $scope.current_user && $scope.current_user.id > 0;
			};

			/**
			 * @param {String} type
			 * @param {String} message
			 */
			$scope.addFlash = function (message, type) {
				type = type || 'success';
				$scope.flash_bag.push({
					type: type,
					message: message
				});
			};

			$scope.$on('sensor.value', function (event) {
				console.log(event);
				var text = '{0}: {1}'.format(event.sensor_vo.name, event.value_formatted);
				App.showNotification(text);
			});

			$scope.$on('espeak.speak', function (event) {
				App.showNotification(event.espeak.text);
			});
		}]);

		App.ng.config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {
			$routeProvider.when("/gpio", {
				templateUrl: "/templates/gpio.html",
				controller: "GpioController"
			}).when("/espeak", {
				templateUrl: "/templates/espeak.html",
				controller: "EspeakController"
			}).when("/egg_timer", {
				templateUrl: "/templates/egg_timer.html",
				controller: "EggTimerController"
			}).when("/radio", {
				templateUrl: "/templates/radio.html",
				controller: "RadioController"
			}).when("/status", {
				templateUrl: "/templates/status.html",
				controller: "StatusController"
			}).when("/sensor", {
				templateUrl: "/templates/sensor.html",
				controller: "SensorController"
			}).when("/todo", {
				templateUrl: "/templates/todo.html",
				controller: "TodoController"
			}).when("/webcam", {
				templateUrl: "/templates/webcam.html",
				controller: "WebcamController"
			}).when("/login", {
				templateUrl: "/templates/user/login.html",
				controller: "LoginController"
			}).when("/register", {
				templateUrl: "/templates/user/register.html",
				controller: "RegisterController"
			}).when("/blog", {
				templateUrl: "/templates/blog.html",
				controller: "BlogController"
			}).when("/mood", {
				templateUrl: "/templates/mood.html",
				controller: "MoodController"
			}).when("/logout", {
				templateUrl: "/templates/mood.html",
				controller: "LogoutController"
			}).when("/dashboard", {
				templateUrl: "/templates/dashboard.html",
				controller: "DashboardController"
			}).when("/user/change_password", {
				templateUrl: "/templates/user/change_password.html",
				controller: "ChangePasswordController"
			}).when("/user/", {
				templateUrl: "/templates/user/user.html",
				controller: "UserController"
			}).when("/user/otp", {
				templateUrl: "/templates/user/otp.html",
				controller: "OtpController"
			}).otherwise({
				templateUrl: "/templates/index.html",
				controller: "IndexController"
			});
		}]);
	},

	/**
	 * @param {String} title
	 */
	changeTitle: function(title) {
		App.$scope.title = title;
	}
};

App.ng.filter('notEmpty', function() {
	return function(input) {
		if (!input) {
			return false;
		}
		return Object.keys(input).length > 0;
	};
});

require.config({
	paths: {
	}
});

$(document).ajaxError(function (event, request) {
	var json;
	if (request.responseText && (json = JSON.parse(request.responseText))) {
		console.log(arguments);
		if (json.error) {
			alert(json.error);
			App.Layout.$scope.addFlash(json.error, 'warning');
		}
	}
});

$(function() {
	"use strict";

	//Enable sidebar toggle
	document.getElementById('offcanvas').onclick = function(e) {
		e.preventDefault();

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
	window.onresize = function() {
		_fix();
	};
});

