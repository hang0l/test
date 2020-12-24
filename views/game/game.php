<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use app\assets\AppAsset;

?>

<?php

AppAsset::register($this);

?>

<?php

Yii::$app->view->registerJs("var json_objects_users = " . Json::encode($json_objects_users)
    . ";",
    \yii\web\View::POS_HEAD);

?>


<div style="width: 200px; position: absolute; right: 150px; top: 150px;">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($user, 'username')->textInput() ?>
    <?= $form->field($figure, 'shape')->dropDownList([
        'square' => 'Square',
        'circle' => 'Circle',
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Create figure', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <button type="button" class="btn btn-primary" id="delete">Delete figure</button>

</div>