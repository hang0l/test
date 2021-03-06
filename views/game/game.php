<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use app\models\Player;
use yii\widgets\Pjax;


?>



<?php

Yii::$app->view->registerJs("var players = " . Json::encode($players)
    . ";",
    \yii\web\View::POS_HEAD);
Yii::$app->view->registerJsFile('js/game.js');
?>

<!-- Grid starts -->
<div id = "scoreTable" style="width: 400px; position: absolute; left: 820px; top: 200px;">
    <?php Pjax::begin(['id' => 'pjax_1']); ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'username',
            [
                'class' => 'yii\grid\DataColumn',
                'label' => 'Figures',
                'value' => function ($data) //правильно ли я понял, что $data "берется" из $dataProvider? автоматически?
                {
                    return 'Circles: ' . $data->getFigureInformation()['circle'] . ', ' .
                        'Squares: ' . $data->getFigureInformation()['square'] . ', ' .
                     'Triangles: ' . $data->getFigureInformation()['triangle'] . ', ' .
                    'Hexagons: ' . $data->getFigureInformation()['hexagon'];
                }
            ]
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
    <button type="button" class="btn btn-primary" id="updateTable">Update Table</button>
</div>
<!-- Grid end-->

<div style="width: 200px; position: absolute; right: 150px; top: 10px;">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username">
    <label for="figure">Figure:</label>
    <select id="figure" name="figure">
        <option value="circle">Circle</option>
        <option value="square">Square</option>
        <option value="triangle">Triangle</option>
        <option value="hexagon">Hexagon</option>
    </select>
    <button type="button" class="btn btn-primary" id="createFigure">Create figure</button>
    <button type="button" class="btn btn-primary" id="deleteFigure">Delete figure</button>
    <a href="<?= Url::toRoute(['game/sign-in'])?>">
        <button type="button" class="btn btn-primary">See your figures</button>
    </a>
</div>