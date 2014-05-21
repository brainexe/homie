function ThoughtBubble(thought) {
	this.bitmap = new createjs.Bitmap("/mood/img/thought_bubble.svg");
	this.bitmap.scaleX = this.bitmap.scaleY = 0.2;
	this.addChild(this.bitmap);

	this.label = new createjs.Text('', 'bold 15px Arial', '#000');
	this.label.x = 10;
	this.label.y = 22;

	this.setText(thought);

	this.addChild(this.label);
}
ThoughtBubble.prototype = new createjs.Container();

ThoughtBubble.prototype.setText = function(thought) {
	this.label.text = thought;
	this.label.scaleX = Math.min(1, 6 / thought.length);
	this.label.scaleY = Math.min(1, 6 / thought.length);

	if (thought.length > 0) {
		this.alpha = 1;
	} else {
		this.alpha = 0;
	}
};
