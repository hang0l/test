<?php
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

AppAsset::register($this);
?>

<?php $this->beginPage() ?>

<?php
/*
 * Файл /views/layouts/game.php
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Game</title>
    <?= Html::jsFile('@web/js/snap.svg-min.js') ?>
<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= Html::jsFile('@web/js/game.js') ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>