<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use app\models\Users;

?>



<?php

Yii::$app->view->registerJs("var users = " . Json::encode($users)
    . ";",
    \yii\web\View::POS_HEAD);

?>

<div style="position: absolute; right: 150px; top: 250px;">
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Figures</th>
        </tr>
        <?php
        $query = Users::find()->asArray()->all();

        $i = 0;
        foreach ($query as $object) {
            echo "<tr>";
            echo "<td>" . $object['id'] . "</td><td>" . $object['username'] .
                "</td><td>" . Users::findOne($object['id'])->getFiguresInformation() . "</td>";
            echo "</tr>";

            $i++;
        }
        ?>
    </table>
</div>


<div style="width: 200px; position: absolute; right: 150px; top: 10px;">

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

    <button type="button" class="btn btn-primary" id="deleteFigure">Delete figure</button>

</div>