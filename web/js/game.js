let canvas = Snap(800, 600);
fillCanvas = canvas.rect(0, 0, 800, 600);
fillCanvas.attr({
	fill: 'gray',
});

class Player {
	constructor(obj) {
		this.playerObject = obj;
		this.name = this.playerObject['username'];
		this.figuresList = [];
	}

	createFigures() {
		for(let i = 0; i < this.playerObject['figure'].length; i++) {
			let figure = new Figure(this.playerObject['figure'][i], this.name);
			this.figuresList.push(figure);
			figure.draw();
		}
	}

	createFigure()
	{
		let figure = new Figure(this.playerObject['figure'], this.name);
		this.figuresList.push(figure);
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
			url: 'index.php/game/update-coords/', // + id + '/',
			type: 'POST',
			cache: false,
			data: {'id': id, 'x': this.x, 'y': this.y},
			beforeSend: function () {
				$("#createFigure").prop("disabled", true);
			},
			success: function (data) {
				$("#createFigure").prop("disabled", false);
			},
			error: function(xhr, textStatus, errorThrown) {
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
				$("#createFigure").prop("disabled", false);;
				let playerObject = {};
				playerObject['username'] = data['player']['username'];
				playerObject['figure'] = data['figure'];
				let player = new Player(playerObject);
				player.createFigure();
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