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
		this.isActive = obj['isActive'];
		this.isSelected = 0;
		this.figure = 0;
		this.text = 0;
		this.group = 0;
		this.toDelete = 0;
		this.r = 25; // радиус, записано так для краткости. У всех фигур одинаковый
		this.collisionDistance = this.r * 2; //диаметр. С его помощью будет проверятся соприкосновение фигур
  };
  	draw(){
  		if (this.isActive == true) {
			if (this.shape === 'square') {
				this.figure = canvas.rect(this.x - this.r, this.y - this.r, this.r * 2, this.r * 2);
				this.text = canvas.text(this.x - this.r, this.y - this.r, this.name);
			} else if (this.shape === 'triangle') {
				this.figure = canvas.polygon(this.x, this.y - this.r, this.x + this.r,
					this.y + this.r / 1.5, this.x - this.r, this.y + this.r / 1.5);
				this.text = canvas.text(this.x - 17, this.y - this.r, this.name);
			} else if (this.shape === 'hexagon') {
				this.figure = canvas.polygon(this.x, this.y - this.r,
					this.x + this.r, this.y - this.r / 1.5, this.x + this.r,
					this.y + this.r / 1.5, this.x, this.y + this.r,
					this.x - this.r, this.y + this.r / 1.5, this.x - this.r,
					this.y - this.r / 1.5);
				this.text = canvas.text(this.x - this.r, this.y - this.r, this.name);
			} else {
				this.figure = canvas.circle(this.x, this.y, this.r);
				this.text = canvas.text(this.x - this.r, this.y - this.r, this.name);
			}
			this.figure.attr({
				fill: 'black',
			});
			this.group = canvas.group(this.figure, this.text);
			let bbox = this.group.getBBox();
			this.x = bbox['cx'];
			this.y = bbox['cy'];
			this.figure.mousedown(this.selectFigure.bind(this));
			this.group.drag();
		}
  	}

  	selectFigure() {
		this.figure.mouseup(this.updateCoord.bind(this));
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
		let bbox = this.group.getBBox();
		this.x = bbox['cx'];
		this.y = bbox['cy'];
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
			},
			error: function (xhr, textStatus, errorThrown) {
				alert('Error: ' + xhr.responseText);
			},
		});
  	}

  	checkCollision()
	{
		$.ajax({
			url: 'index.php/game/check-collision/', // + id + '/',
			type: 'POST',
			cache: false,
			data: {'id': this.id, 'x': this.x, 'y': this.y,
			'collisionDistance': this.collisionDistance, 'name': this.name},
			success: (data) => {
				let id = data['id'];
				for (let i = 0; i < figuresList.length; i ++)
				{
					if (parseInt(id) === parseInt(figuresList[i]['id']))
					{
						remove(figuresList[i]['id'], figuresList[i]['figure'], figuresList[i]['text']);
						figuresList.splice(i, 1);
					}
				}

			},
			error: function (xhr, textStatus, errorThrown) {
				alert('Error: ' + xhr.responseText);
			},
		});
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
				for (let i = 0; i < figuresList.length; i ++)
				{
					if (id === parseInt(figuresList[i]['id'])) {
						figuresList.splice(i, 1);
					}
				}
				$("#deleteFigure").prop("disabled", false);
			},
			error: function (xhr, textStatus, errorThrown) {
				alert('Error: ' + xhr.responseText);
			},
		});
	});
});


function remove(id, figure, text)
{
	$.ajax({
		url: 'index.php/game/delete-object/',
		type: 'POST',
		cache: false,
		data: {'id': id},
		success: function () {
			figure.remove();
			text.remove();
		},
		error: function (xhr, textStatus, errorThrown) {
			alert('Error: ' + xhr.responseText);
		},
	});
}