let canvas = Snap(800, 600);
fillCanvas = canvas.rect(0, 0, 800, 600);
fillCanvas.attr({
	fill: 'gray',
});

var objects = JSON.parse(json_objects);

class Figure {
	constructor(obj) {
	this.id = obj['id'];
    this.name = obj['username'];
    this.shape = obj['shape'];
    this.xCoord = Number(obj['xCoord']);
    this.yCoord = Number(obj['yCoord']);
    this.isSelected = 0;
    this.figure = 0;
    this.text = 0;
    this.group = 0;
  };

  	draw(){
  		if (this.shape == 'square') {
  			this.figure = canvas.rect(this.xCoord, this.yCoord, 50, 50);
  			this.text = canvas.text(this.xCoord, this.yCoord, this.name);
  		}
  		else {
  			this.figure = canvas.circle(this.xCoord, this.yCoord, 20);
  			this.text = canvas.text(this.xCoord - 20, this.yCoord - 20, this.name);
  		}
  		this.figure.attr({
  			fill: 'black',
  		});
  		this.group = canvas.group(this.figure, this.text);
  		this.group.drag();
  		this.figure.mousedown(this.selectFigure.bind(this));
  		this.figure.mouseup(this.updateCoord.bind(this));

  	}

  	selectFigure() {
  		if (this.isSelected == 0) {
  			this.figure.attr({
  				fill: 'red',
  			});
  			this.isSelected = 1;
  			this.deleteObj(this.id, this.figure, this.text);
  		}
  		else {
  			this.figure.attr({
  				fill: 'black',
  			});
  			this.isSelected = 0;
  		}
  	}

  	deleteObj(id, figure, text) {
  		$("#delete").on("click", function(){
			figure.remove();
			text.remove();
			$.ajax({
				url: 'index.php?r=site%2Fdelete-object',
				type: 'POST',
				cache: false,
				data: JSON.stringify({'id': id}),
				dataType: 'json',
				contentType: 'application/json',
				beforeSend: function() {
					$("delete").prop("disabled", true);
				},
				success: function() {
					$("delete").prop("disabled", false);
				}	
			});
		});
  	}

  	updateCoord() {
  		let id = this.id;
  		let bbox = this.group.getBBox();
  		if (this.shape == 'square') {
  			this.xCoord = bbox['x'] + 1; //костыли - BBox() получает координаты группы, а не 
  			this.yCoord = bbox['y'] + 13; //фигур, поэтому при перерисовке они смещаются
  		}
  		else {
  			this.xCoord = bbox['cx'] + 1;
  			this.yCoord = bbox['cy'] + 7;
  		}
  		$.ajax({
			url: 'index.php?r=site%2Fupdate-coords',
			type: 'POST',
			cache: false,
			data: JSON.stringify({'id': id, 'xCoord': this.xCoord, 'yCoord': this.yCoord}),
			dataType: 'json',
			contentType: 'application/json',	
		});
  	}
}

for(let i=0; i<objects.length; i++) {
	let figure = new Figure(objects[i]);
	figure.draw();
}