function Eye() {
	// workaround to avoid that eyes share their graphics object..
	this.graphics = new createjs.Graphics();
	this.state = 'open';
	this.next_blink = Math.floor(Math.random() * 1000) + 1;

	this.open();
}

Eye.prototype = new createjs.Shape();

Eye.prototype.open = function() {
	this.state = 'open';
	this.graphics.clear();
	this.graphics.beginFill('#000');
	this.graphics.drawCircle(3, 3, 6);
};

Eye.prototype.close = function() {
	this.state = 'closed';
	this.graphics.clear();
	this.graphics.beginFill('#000');
	this.graphics.drawEllipse(-4, 1.5, 14, 3);
};

Eye.prototype.blink = function(next_blink) {
	this.next_blink = next_blink;
	if (this.state == 'open') {
		this.close();
	} else {
		this.open();
	}
};
