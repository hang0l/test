<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'shape',
        [
            'class' => 'yii\grid\DataColumn',
            'label' => 'Is active',
            'value' => function ($data)
            {
                if ($data->isActive) {
                    return 'Active';
                } else {
                    return 'Deleted';
                }
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{restore}',
            'buttons' => [
                'restore' => function ($url, $model, $key) {
                    if ($model->isActive) {
                        return 'Your figure is active';
                    } else {
                        return Html::a('Restore figure',
                            ['game/restore-figure', 'id' => $model->id]);
                    }
                }
            ],
        ],
    ]
]);

?>