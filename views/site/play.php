<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<script>
let canvas = Snap(800, 600);
canvas.addClass("canv");
fillCanvas = canvas.rect(0, 0, 800, 600);
fillCanvas.attr({
	fill: 'gray',
});
let json_objects = '<?php echo $json_objects;?>';
let objects = JSON.parse(json_objects);

class Square {
	constructor(obj) {
	this.id = obj['id'];
    this.name = obj['username'];
    this.x_coord = Number(obj['x_coord']);
    this.y_coord = Number(obj['y_coord']);
    this.isSelected = 0;
    this.rectangle = 0;
    this.text = 0;
    this.group = 0;
  };

  	draw(){
  		this.rectangle = canvas.rect(this.x_coord, this.y_coord, 50, 50);
  		this.text = canvas.text(this.x_coord, this.y_coord, this.name);
  		this.rectangle.attr({
  			fill: 'black',
  		});
  		this.group = canvas.group(this.rectangle, this.text);
  		this.group.drag();
  		this.rectangle.mousedown(this.selectSquare.bind(this));
  		this.rectangle.mouseup(this.updateCoord.bind(this));

  	}

  	selectSquare() {
  		if (this.isSelected == 0) {
  			this.rectangle.attr({
  				fill: 'red',
  			});
  			this.isSelected = 1;
  			this.deleteObj(this.id, this.rectangle, this.text);
  		}
  		else {
  			this.rectangle.attr({
  				fill: 'black',
  			});
  			this.isSelected = 0;
  		}
  	}

  	deleteObj(id, rectangle, text) {
  		$("#delete").on("click", function(){
			rectangle.remove();
			text.remove();
			$.ajax({
				url: 'index.php?r=site%2Fdeleteobject',
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
  		let x_coord = bbox['x'];
  		let y_coord = bbox['y'];
  		$.ajax({
			url: 'index.php?r=site%2Fupdatecoords',
			type: 'POST',
			cache: false,
			data: JSON.stringify({'id': id, 'x_coord': x_coord, 'y_coord': y_coord}),
			dataType: 'json',
			contentType: 'application/json',	
		});
  	}
}

for(let i=0; i<objects.length; i++) {
	let square = new Square(objects[i]);
	square.draw();
}

</script>


<div style="width: 200px; position: absolute; right: 15px; top: 150px;">
	<?php $form = ActiveForm::begin(); ?>

		   <?= $form->field($model, 'username')->textInput() ?>

		   <div class="form-group">
		        <?= Html::submitButton('Create object', ['class' => 'btn btn-primary']) ?>
		   </div>

	<?php ActiveForm::end(); ?>
	<button type="button" class="btn btn-primary" id="delete">Delete square</button>
</div>