<?php

use yii\helpers\Html;
use app\assets\AppAsset;
AppAsset::register($this);

?>



<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>Game</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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


<?= $content ?>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
