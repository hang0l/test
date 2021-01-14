let canvas = Snap(800, 600);
fillCanvas = canvas.rect(0, 0, 800, 600);
fillCanvas.attr({
	fill: 'gray',
});

let figuresList = [];
let figureToDelete = {};

class Player {
	constructor(obj) {
		this.playerObject = obj;
		this.name = this.playerObject['username'];
		this.figuresList = [];
	}

	createFigures() {
		for(let i = 0; i < this.playerObject['figure'].length; i++) {
			let figure = new Figure(this.playerObject['figure'][i], this.name);
			figuresList.push(figure);
			figure.draw();
		}
	}

	createFigure()
	{
		let figure = new Figure(this.playerObject['figure'], this.name);
		//this.figuresList.push(figure);
		figuresList.push(figure);
		figure.draw();
	}
}

class Figure {
	constructor(obj, name) {
		this.id = obj['id'];
		this.player = obj['player_id']
		this.name = name;
		this.shape = obj['shape'];
		this.x = Number(obj['x']);
		this.y = Number(obj['y']);
		this.isSelected = 0;
		this.figure = 0;
		this.text = 0;
		this.group = 0;
		this.toDelete = 0;
		this.cx = 0;
		this.cy = 0;
		this.shapeNumber = 0;
		this.r = 25; // радиус, записано так для краткости. У всех фигур одинаковый
		this.d = this.r * 2; //диаметр. С его помощью будет проверятся соприкосновение фигур
  };
  	draw(){
  		if (this.shape === 'square') {
  			this.figure = canvas.rect(this.x, this.y, this.r * 2, this.r * 2);
  			this.text = canvas.text(this.x, this.y, this.name);
  			this.shapeNumber = 4;
  		}
  		else if (this.shape === 'triangle')
		{
			this.figure = canvas.polygon(this.x, this.y, this.x -this.r,
				this.y + this.r, this.x + this.r, this.y + this.r);
			this.text = canvas.text(this.x - 17, this.y, this.name);
			this.shapeNumber = 3;
		}
		else if (this.shape === 'hexagon')
		{
			this.figure = canvas.polygon(this.x, this.y, this.x + this.r, this.y + this.r / 1.5,
				this.x + this.r, this.y + this.r * 1.5, this.x, this.y + this.r * 2,
				this.x - this.r, this.y + this.r * 1.5, this.x - this.r, this.y + this.r / 1.5,
				this.x, this.y);
			this.text = canvas.text(this.x, this.y, this.name);
			this.shapeNumber = 6;
		}
  		else {
  			this.figure = canvas.circle(this.x, this.y, this.r);
  			this.text = canvas.text(this.x - this.r, this.y - this.r, this.name);
			this.shapeNumber = 10;
  		}
  		this.figure.attr({
  			fill: 'black',
  		});
  		this.group = canvas.group(this.figure, this.text);
		let bbox = this.group.getBBox();
		this.cx = bbox['cx'];
		this.cy = bbox['cy'];
  		this.figure.mousedown(this.selectFigure.bind(this));
  	}

  	selectFigure() {
		this.figure.mouseup(this.updateCoord.bind(this));
		this.group.drag();
  		if (this.isSelected === 0) {
  			this.figure.attr({
  				fill: 'red',
  			});
  			this.isSelected = 1;
			figureToDelete.id = this.id;
			figureToDelete.figure = this.figure;
			figureToDelete.text = this.text;
  		}
  		else {
  			this.figure.attr({
  				fill: 'black',
  			});
  			this.isSelected = 0;
  		}
  		for (let i = 0; i < figuresList.length; i++)
		{
			if (this.id !== figuresList[i]['id'])
			{
				if (figuresList[i]['isSelected'] === 1)
				{
					figuresList[i].selectFigure();
				}
			}
		}
  	}

  	updateCoord() {
  		console.log('Method is working');
		let bbox = this.group.getBBox();
		this.cx = bbox['cx'];
		this.cy = bbox['cy'];
		let id = this.id;
		if (this.shape === 'circle') {
			this.x = bbox['cx'] + 1;
			this.y = bbox['cy'] + 7;
		} else {
			this.x = bbox['x'] + 1; //костыли - BBox() получает координаты группы, а не
			this.y = bbox['y'] + 13; //фигур, поэтому при перерисовке они смещаются
		}
		$.ajax({
			url: 'index.php/game/update-coords/', // + id + '/',
			type: 'POST',
			cache: false,
			data: {'id': id, 'x': this.x, 'y': this.y},
			beforeSend: function () {
				$("#createFigure").prop("disabled", true);
			},
			success: (data) => {
				$("#createFigure").prop("disabled", false);
				this.checkCollision()
				this.figure.unmouseup();
				this.group.undrag();
			},
			error: function (xhr, textStatus, errorThrown) {
				alert('Error: ' + xhr.responseText);
			},
		});
  	}

  	checkCollision()
	{
		for(let i = 0; i < figuresList.length; i ++)
		{
			if (this.id !== figuresList[i]['id'])
			{
				let figureCx = figuresList[i]['cx'];
				let figureCy = figuresList[i]['cy'];
				if (this.cx > figureCx)
				{
					if (this.cy > figureCy)
					{
						if (this.cx - figureCx < this.d && this.cy - figureCy < this.d)
						{
							this.deleteCollisionFigures(i);
						}
					}
					else
					{
						if (this.cx - figureCx < this.d && figureCy - this.cy < this.d)
						{
							this.deleteCollisionFigures(i);
						}
					}
				}
				else
				{
					if (this.cy > figureCy)
					{
						if (figureCx - this.cx < this.d && this.cy - figureCy < this.d)
						{
							this.deleteCollisionFigures(i);
						}
					}
					else
					{
						if (figureCx - this.cx < this.d && figureCy - this.cy < this.d)
						{
							this.deleteCollisionFigures(i);
						}
					}
				}
			}
		}
	}

	remove(id, figure, text)
	{
		$.ajax({
			url: 'index.php/game/delete-object/',
			type: 'POST',
			cache: false,
			data: {'id': id},

			beforeSend: function () {
				$("#deleteFigure").prop("disabled", true);
			},
			success: function () {
				figure.remove();
				text.remove();
				$("#deleteFigure").prop("disabled", false);
			},
			error: function (xhr, textStatus, errorThrown) {
				alert('Error: ' + xhr.responseText);
			},
		});
	}

	deleteCollisionFigures(i) {
		if (this.name !== figuresList[i]['name']) {
			if (this.shapeNumber > figuresList[i]['shapeNumber']) {
				figuresList[i].remove(figuresList[i]['id'], figuresList[i]['figure'],
					figuresList[i]['text']);
				return true;
			} else if (this.shapeNumber < figuresList[i]['shapeNumber']) {
				this.remove(this.id, this.figure, this.text);
				return true;
			}
		}
	}
}

for(let i=0; i < players.length; i++) {
	let player = new Player(players[i]);
	player.createFigures();
}
$(document).ready(function() {
	$("#createFigure").on("click", function () {
		let username = $('#username').val();
		let figure = $('#figure').val();
		$.ajax({
			url: 'index.php/game/create-figure/',
			type: 'POST',
			cache: false,
			data: {'username': username, 'shape': figure},
			beforeSend: function () {
				$("#createFigure").prop("disabled", true);
			},
			success: function (data) {
				$("#createFigure").prop("disabled", false);
				$("#username").val('');
				if (data['error']){
					alert(data['error']['username']);
				} else {
					let playerObject = {};
					playerObject['username'] = data['player']['username'];
					playerObject['figure'] = data['figure'];
					let player = new Player(playerObject);
					player.createFigure();
				}
			},
			error: function (xhr, textStatus, errorThrown) {
				alert('Error: ' + xhr.responseText);
			},
		});
	});
});

$(document).ready(function() {
	$("#updateTable").on("click", function () {
		$.pjax.reload({container: '#pjax_1'})
	});
});

$(document).ready(function() {
	$("#deleteFigure").on("click", function () {
		let id = figureToDelete['id'];
		let figure = figureToDelete['figure'];
		let text = figureToDelete['text'];
		$.ajax({
			url: 'index.php/game/delete-object/',
			type: 'POST',
			cache: false,
			data: {'id': id},
			beforeSend: function () {
				$("#deleteFigure").prop("disabled", true);
			},
			success: function () {
				figure.remove();
				text.remove();
				$("#deleteFigure").prop("disabled", false);
			},
			error: function (xhr, textStatus, errorThrown) {
				alert('Error: ' + xhr.responseText);
			},
		});
	});
});