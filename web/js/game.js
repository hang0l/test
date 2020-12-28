let canvas = Snap(800, 600);
fillCanvas = canvas.rect(0, 0, 800, 600);
fillCanvas.attr({
	fill: 'gray',
});

//let objects = JSON.parse(json_objects_users);

class User {
	constructor(obj) {
		this.userObject = obj;
		this.name = this.userObject['username'];
		this.figuresList = [];
	}

	createFigures() {
		for(let i = 0; i < this.userObject['figures'].length; i++) {
			let figure = new Figure(this.userObject['figures'][i], this.name);
			this.figuresList.push(figure);
			figure.draw();
		}
	}
}

class Figure {
	constructor(obj, name) {
		this.id = obj['id'];
		this.user_id = obj['user_id']
		this.name = name;
		this.shape = obj['shape'];
		this.x = Number(obj['x']);
		this.y = Number(obj['y']);
		this.isSelected = 0;
		this.figure = 0;
		this.text = 0;
		this.group = 0;
		this.toDelete = 0;
  };

  	draw(){
  		if (this.shape == 'square') {
  			this.figure = canvas.rect(this.x, this.y, 50, 50);
  			this.text = canvas.text(this.x, this.y, this.name);
  		}
  		else {
  			this.figure = canvas.circle(this.x, this.y, 20);
  			this.text = canvas.text(this.x - 20, this.y - 20, this.name);
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
  		if (this.isSelected === 0) {
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
  		if (this.toDelete === 0) {
  			this.toDelete = 1;
			$("#deleteFigure").on("click", function () {
				$.ajax({
					url: 'index.php?r=game%2Fdelete-object',
					type: 'POST',
					cache: false,
					data: {'id': id},

					beforeSend: function () {
						$("delete").prop("disabled", true);
					},
					success: function () {
						figure.remove();
						text.remove();
						$("delete").prop("disabled", false);
					},
					error: function (xhr, textStatus, errorThrown) {
						alert('Error: ' + xhr.responseText);
					},
				});
			});
		}
  	}

  	updateCoord() {
  		let id = this.id;
  		let bbox = this.group.getBBox();
  		if (this.shape === 'square') {
  			this.x = bbox['x'] + 1; //костыли - BBox() получает координаты группы, а не
  			this.y = bbox['y'] + 13; //фигур, поэтому при перерисовке они смещаются
  		}
  		else {
  			this.x = bbox['cx'] + 1;
  			this.y = bbox['cy'] + 7;
  		}
  		$.ajax({
			url: 'index.php?r=game%2Fupdate-coords',
			type: 'POST',
			cache: false,
			data: {'id': id, 'x': this.x, 'y': this.y},
			error: function(xhr, textStatus, errorThrown) {
				alert('Error: ' + xhr.responseText);
            },
		});
  	}
}

for(let i=0; i < users.length; i++) {
	let user = new User(users[i]);
	user.createFigures();
}