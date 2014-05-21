var tearsCounter = 0;

function Tear(initialRadius, eye) {
	this.initialRadius = initialRadius;
	this.eye = eye;
	this.options = defaults = {
		'cryMood': 30,
		'duration': 4000
	};

	this.side = tearsCounter++ % 2;

	this.graphics.beginFill('#A9E1FF');

	this.graphics.moveTo(15, 0);
	this.graphics.bezierCurveTo(
		0, 20,
		30, 20,
		15, 0
	);
	this.graphics.endFill();

	this.alpha = 0;
}

Tear.prototype = new createjs.Shape();

Tear.prototype.update = function (time, lastTime, mood) {
	if (mood <= this.options.cryMood) {
		var duration = this.options.duration,
			totalTearsTime = time % (duration * 2),
			active = (totalTearsTime < duration && this.side == 0) || (totalTearsTime >= duration && this.side == 1),
			relativeTearsTime = totalTearsTime % duration;

		if (active) {
			this.alpha = 1;
			this.scaleX = this.scaleY = (relativeTearsTime < duration / 2) ? relativeTearsTime / (duration / 2) : 1;
			this.x = this.eye.x + 3 - this.scaleX * 15;
			this.y = this.eye.y + 6 + ((relativeTearsTime >= duration / 2) ? (relativeTearsTime - duration / 2) / (duration / 2) * this.initialRadius * 0.2 : 0);
		} else {
			this.alpha = 0;
		}
	} else {
		this.alpha = 0;
	}
};
