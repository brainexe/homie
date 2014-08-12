
App.ng.controller('MoodController', ['$scope', function ($scope) {
	require(['mood'], function(){
		$.get('/blog/mood/', function(data) {
			init(data);
		});

		var canvas = window.document.getElementById('mood_stage');
		var stage = new createjs.Stage(canvas);

		var INITIAL_SMILEY_RADIUS = 100;
		var thought = {
				onStage: false,
				object: null,
				label: null,
				text: ''
			},
			mood = {
				target: 50,
				currentValue: 50,
				object: null
			},
			lastTime = 0,
			changed = false;

		var r = 0, g = 0, b = 0, color_states = {}, color, i, diff;
		color_states[100] = {r: 0, g: 200, b: 0, next: null};
		color_states[50] = {r: 255, g: 210, b: 0, next: color_states[100]};
		color_states[0] = {r: 180, g: 30, b: 0, next: color_states[50]};

		function rgbToHex(r, g, b) {
			r = r <= 0 ? '00' : r.toString('16');
			g = g <= 0 ? '00' : g.toString('16');
			b = b <= 0 ? '00' : b.toString('16');

			r = r.length == 1 ? '0' + r : r;
			g = g.length == 1 ? '0' + g : g;
			b = b.length == 1 ? '0' + b : b;

			return '#' + r + g + b;
		}

		function getColorForMood(mood) {
			for (i = 0; i <= 100; i += 50) {
				color = color_states[i];

				if (mood <= (i + 50)) {
					diff = mood - i;

					r = color.r + Math.floor((color.next.r - color.r) * diff / 50);
					g = color.g + Math.floor((color.next.g - color.g) * diff / 50);
					b = color.b + Math.floor((color.next.b - color.b) * diff / 50);

					break;
				}
			}

			return rgbToHex(r, g, b);
		}

		function Smiley(mood) {
			var mood_color = getColorForMood(mood);
			this.background = new Head(mood_color, INITIAL_SMILEY_RADIUS);

			this.left_eye = new Eye();
			this.right_eye = new Eye();

			this.left_eye.x = 67;
			this.right_eye.x = 127;
			this.left_eye.y = this.right_eye.y = INITIAL_SMILEY_RADIUS - INITIAL_SMILEY_RADIUS * 0.4;

			this.left_tear = new Tear(INITIAL_SMILEY_RADIUS, this.left_eye);
			this.right_tear = new Tear(INITIAL_SMILEY_RADIUS, this.right_eye);

			this.mouth = new Mouth(mood, INITIAL_SMILEY_RADIUS);

			this.hand = new Hand();
			this.hand.x = INITIAL_SMILEY_RADIUS * 1.2 + 70;
			this.hand.y = INITIAL_SMILEY_RADIUS * 0.7 + 120;

			this.addChild(this.background);
			this.addChild(this.left_eye);
			this.addChild(this.right_eye);
			this.addChild(this.left_tear);
			this.addChild(this.right_tear);
			this.addChild(this.mouth);
			this.addChild(this.hand);
		}

		Smiley.prototype = new createjs.Container();

		Smiley.prototype.setMood = function (mood) {
			var mood_color = getColorForMood(mood);
			this.background.setColor(mood_color);

			this.mouth.changeShape(mood);
		};

		Smiley.prototype.wink = function () {
			var self = this;

			self.left_eye.close();
			self.right_eye.close();
		};

		Smiley.prototype.update = function (time, lastTime) {
			// mood transition ( change color + mouth of the smiley )
			if (mood.target != mood.currentValue) {
				var new_mood;

				var val = Math.floor(time - lastTime) / 500 * (50 - (Math.abs(mood.target - mood.currentValue) / 100 * 40));
				if (mood.target > mood.currentValue) {
					new_mood = Math.min(mood.currentValue + val, mood.target);
				} else if (mood.target < mood.currentValue) {
					new_mood = Math.max(mood.currentValue - val, mood.target);
				}

				mood.currentValue = new_mood;
				this.setMood(new_mood);
			}

			this.left_tear.update(time, lastTime, mood.currentValue);
			this.right_tear.update(time, lastTime, mood.currentValue);

			if ((time >= this.left_eye.next_blink || time >= this.right_eye.next_blink) && (mood.currentValue >= 30 || this.state == 'closed')) {
				var next_blink = this.left_eye.state == 'open' ? time + 200 : time + 100 + Math.floor(Math.random() * 6000);

				this.left_eye.blink(next_blink);
				this.right_eye.blink(next_blink);
			}

			this.hand.update(time);
		};

		(function render(time) {
			if (thought.object) {
				if (thought.text && thought.object.label.text != thought.text) {
					thought.object.setText(thought.text);
				}
			}

			if (mood.target !== mood.currentValue && time > 0) {
				changed = true;
			}

			if (mood.object) {
				mood.object.update(time, lastTime);
			}

			if (changed) {
				changed = false;
			}
			stage.update();

			if (time > 0) lastTime = time;
			window.requestAnimationFrame(render);
		})();

		var isInit = false;
		var init = function (data) {
			mood.target = mood.currentValue = data.mood;
			canvas.width = window.innerWidth;
			canvas.height = window.innerHeight;

			mood.object = new Smiley(mood.currentValue);

			var scale_factor = Math.min(window.innerWidth / (INITIAL_SMILEY_RADIUS * 2), window.innerHeight / (INITIAL_SMILEY_RADIUS * 2));
			mood.object.scaleY = mood.object.scaleX = scale_factor;

			mood.object.x = window.innerWidth / 2 - INITIAL_SMILEY_RADIUS * scale_factor;
			mood.object.y = window.innerHeight / 2 - INITIAL_SMILEY_RADIUS * scale_factor;
			stage.addChild(mood.object);

			thought.object = new ThoughtBubble(data.thought);
			thought.object.scaleX = thought.object.scaleY = scale_factor;
			thought.object.x = window.innerWidth / 2 + INITIAL_SMILEY_RADIUS * 0.75 * scale_factor;
			thought.object.y = window.innerHeight / 2 - INITIAL_SMILEY_RADIUS * scale_factor;

			stage.addChild(thought.object);

			if (canvas.requestFullscreen) {
				canvas.requestFullscreen();
			} else if (canvas.msRequestFullscreen) {
				canvas.msRequestFullscreen();
			} else if (canvas.mozRequestFullScreen) {
				canvas.mozRequestFullScreen();
			} else if (canvas.webkitRequestFullscreen) {
				canvas.webkitRequestFullscreen();
			}

			isInit = true;
		};

		window.onresize = function () {
			if (!isInit) return;

			canvas.width = window.innerWidth;
			canvas.height = window.innerHeight;

			var scale_factor = Math.min(window.innerWidth / (INITIAL_SMILEY_RADIUS * 2), window.innerHeight / (INITIAL_SMILEY_RADIUS * 2));
			mood.object.scaleY = mood.object.scaleX = scale_factor;
			mood.object.x = window.innerWidth / 2 - INITIAL_SMILEY_RADIUS * scale_factor;
			mood.object.y = window.innerHeight / 2 - INITIAL_SMILEY_RADIUS * scale_factor;

			thought.object.scaleX = thought.object.scaleY = scale_factor;
			thought.object.x = window.innerWidth / 2 + INITIAL_SMILEY_RADIUS * 0.75 * scale_factor;
			thought.object.y = window.innerHeight / 2 - INITIAL_SMILEY_RADIUS * scale_factor;
		};
	});

}]);