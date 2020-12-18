<?php

use app\models\Game;


$objects = Game::find()->asArray()->all();
$result = json_encode($objects);
echo $result;

?>