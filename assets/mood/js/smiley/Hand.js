function Hand() {
	this.bitmap = new createjs.Bitmap("/mood/img/Hand.svg");
	this.bitmap.scaleX = this.bitmap.scaleY = 0.2;

	var self = this;

	this.regX = 70;
	this.regY = 120;

	this.alpha = 0;
	this.visible = false;

	this.next_wink = 0;
	
	this.addChild(this.bitmap);
}
Hand.prototype = new createjs.Container();

Hand.prototype.update = function(time) {
	if (this.next_wink <= time) {
		this.visible = !this.visible;

		var next = this.visible ? 3500 : 30000 + Math.floor(Math.random() * 60000);
		this.next_wink = time + next;
		this.alpha = this.visible ? 1 : 0;
	}

	if (this.visible) {
		var framePos = ((time % 5040) / 100) % 360;
		this.rotation = Math.sin(framePos) * 10;
	}
};
