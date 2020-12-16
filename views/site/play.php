<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

	<?php $form = ActiveForm::begin(); ?>

	    <?= $form->field($model, 'username')->textInput() ?>

	    <div class="form-group">
	        <?= Html::submitButton('Create object', ['class' => 'btn btn-primary']) ?>
	    </div>

	<?php ActiveForm::end(); ?>

