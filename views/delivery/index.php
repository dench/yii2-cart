<?php

use dench\cart\models\Delivery;
use dench\sortable\grid\SortableColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Deliveries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Delivery'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return [
                'data-position' => $model->position,
            ];
        },
        'columns' => [
            [
                'class' => SortableColumn::class,
            ],
            'name',
            [
                'attribute' => 'type',
                'content' => function(Delivery $model, $key, $index, $column){
                    $list = Delivery::typeList();
                    return @$list[$model->type];
                },
            ],
            [
                'attribute' => 'enabled',
                'content' => function(Delivery $model, $key, $index, $column){
                    if ($model->enabled) {
                        $class = 'glyphicon glyphicon-ok';
                    } else {
                        $class = '';
                    }
                    return Html::tag('i', '', ['class' => $class]);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
        'options' => [
            'data' => [
                'sortable' => 1,
                'sortable-url' => Url::to(['sorting']),
            ]
        ],
    ]); ?>
</div>
