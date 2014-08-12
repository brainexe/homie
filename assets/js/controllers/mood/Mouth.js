function Mouth(mood, initialRadius) {
	this.initialRadius = initialRadius;
	this.changeShape(mood);
}
Mouth.prototype = new createjs.Shape();

Mouth.prototype.changeShape = function(mood) {
	this.graphics.clear();

	this.graphics.setStrokeStyle(8, 'round', 'round');
	this.graphics.beginStroke('#000');

	this.graphics.moveTo(50, this.initialRadius + this.initialRadius * (0.5 - (mood / 100 * 0.2)));
	this.graphics.bezierCurveTo(
		70, this.initialRadius + this.initialRadius * (0.2 + (mood / 100 * 0.4)),
		130, this.initialRadius + this.initialRadius * (0.2 + (mood / 100 * 0.4)),
		150, this.initialRadius + this.initialRadius * (0.5 - (mood / 100 * 0.2))
	);

	this.graphics.endStroke();
};