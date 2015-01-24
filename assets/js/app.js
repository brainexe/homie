"use strict";
// todo cleanup + extract into single models

/**
 * @deprecated
 * @todo replace by angulat
 * @param element
 */
function togglePanel(element) {
	element.nextElementSibling.classList.toggle('hidden');
}

/**
 * @param {String} filename
 * @returns {String}
 */
function asset(filename) {
	return filename;
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
	},

	/**
	 * @param {String} content
	 */
	showNotification: function (content) {
		if (!("Notification" in window)) {
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
	debug: null,
	$scope: null,

	controllers: [
		// menu
		{controller:'DashboardController', name: 'Dashboard', url: 'dashboard', icon:'th-large', templateUrl: asset('/templates/dashboard.html')},
		{controller:'SensorController', name: 'Sensors', url: 'sensor', icon:'dashboard', templateUrl: asset('/templates/sensor.html')},
		{controller:'GpioController', name: 'GPIO', url: 'gpio', icon:'flash', templateUrl: asset('/templates/gpio.html')},
		{controller:'RadioController', name: 'Home Control', url: 'radio', icon:'home', templateUrl: asset('/templates/radio.html')},
		{controller:'EspeakController', name: 'Speak', url: 'speak', icon:'home', templateUrl: asset('/templates/espeak.html')},
		{controller:'WebcamController', name: 'Webcam', url: 'webcam', icon:'bullhorn', templateUrl: asset('/templates/webcam.html')},
		{controller:'StatusController', name: 'Status', url: 'status', icon:'stats', templateUrl: asset('/templates/status.html')},
		{controller:'BlogController', name: 'Blog', url: 'blog', icon:'paperclip', templateUrl: asset('/templates/blog.html')},
		{controller:'EggTimerController', name: 'Egg Timer', url: 'egg_timer', icon:'time', templateUrl: asset('/templates/egg_timer.html')},
		{controller:'TodoController', name: 'ToDo List', url: 'todo', icon:'list', templateUrl: asset('/templates/todo.html')},
		{controller:'UserController', name: 'User Settings', url: 'user', icon:'user', templateUrl: asset('/templates/user/user.html')},

		// private
		{controller:'LoginController', name: 'Login', url: 'login', icon: 'user', is_public: true, templateUrl: asset('/templates/user/login.html')},
		{controller:'RegisterController', name: 'Register', url: 'register', icon: 'user', is_public: true, templateUrl: asset('/templates/user/register.html')},

		// hidden controllers
		{url: 'logout', templateUrl: "/templates/mood.html", controller: "LogoutController"},
		{url: 'user/change_password', templateUrl: "/templates/user/change_password.html", controller: "ChangePasswordController"},
		{url: 'user/otp', templateUrl: "/templates/user/otp.html", controller: "OtpController"},
		{url: 'templates/index.html', templateUrl: "/", controller: "IndexController"}
	],

	init: function (debug, current_user) {
		App.Layout.debug = debug;

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

				var item = {
					type: type,
					message: message
				};

				$scope.flash_bag.push(item);

				window.setTimeout(function () {
					var index = $scope.flash_bag.indexOf(item);

					if (index > -1) {
						$scope.flash_bag.splice(index, 1);
						$scope.$apply();
					}
				}, 5000);

				$scope.$apply();
			};

			$scope.$on('sensor.value', function (event_name, event) {
				var text = '{0}: {1}'.format(event.sensor_vo.name, event.value_formatted);
				App.showNotification(text);
			});

			$scope.$on('espeak.speak', function (event) {
				App.showNotification(event.espeak.text);
			});

			$scope.$on('$routeChangeSuccess', function (event, current, previous) {
				if (current.$$route.name) {
					App.Layout.changeTitle(current.$$route.name);
				}
			});
		}]);

		App.ng.config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {
			for (var i in App.Layout.controllers) {
				var metadata = App.Layout.controllers[i];
				$routeProvider.when('/' + metadata.url, metadata);
			}
		}]);
	},

	/**
	 * @param {String} title
	 * @todo fix
	 */
	changeTitle: function (title) {
		document.title = title;
		//App.$scope.title = title;
	}
};

//TODO use more arrays in templates
App.ng.filter('notEmpty', function () {
	return function (input) {
		if (!input) {
			return false;
		}
		return Object.keys(input).length > 0;
	};
});

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
	document.getElementById('offcanvas').onclick = function (e) {
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
	window.onresize = function () {
		_fix();
	};
});

window['init'] = function (debug, user_vo, socket_server) {
	App.init(debug, user_vo, socket_server);
};
