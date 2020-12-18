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
    <script
        src="https://code.jquery.com/jquery-1.12.3.min.js"
        integrity="sha256-aaODHAgvwQW1bFOGXMeX+pC4PZIPsvn2h1sArYOhgXQ="
        crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="/js/snap.svg-min.js"></script>
<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Game', 'url' => ['/site/play']],
        ],
    ]);
    NavBar::end();
    ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?= $content ?>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>