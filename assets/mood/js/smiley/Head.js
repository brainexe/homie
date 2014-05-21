function Head(mood_color, initialRadius) {
	this.initialRadius = initialRadius;
	this.setColor(mood_color);
}
Head.prototype = new createjs.Shape();

Head.prototype.setColor = function (color) {
	this.graphics.clear();
	this.graphics.setStrokeStyle(9, 'round', 'round');
	this.graphics.beginStroke('#000');
	this.graphics.beginFill(color);
	this.graphics.drawCircle(this.initialRadius, this.initialRadius, this.initialRadius);
	this.graphics.endStroke();
};