<?php

use dench\cart\models\Order;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel dench\cart\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <!--<p>
        <?= Html::a(Yii::t('app', 'Create order'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'label' => '№',
                'headerOptions' => ['width' => '100'],
            ],
            [
                'attribute' => 'buyer_name',
                'value' => 'buyer.name',
                'label' => Yii::t('app', 'Buyer')
            ],
            [
                'attribute' => 'phone',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'amount',
                'headerOptions' => ['width' => '100'],
                'enableSorting' => false,
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'filter' => false,
            ],
            [
                'attribute' => 'status',
                'filter' => Order::statusList(),
                'enableSorting' => false,
                'content' => function($model, $key, $index, $column){
                    $statusList = Order::statusList();
                    $classes = Order::statusClass();
                    $class = $classes[$model->status];
                    return '<span class="badge badge-' . $class . '">' . $statusList[$model->status] . '</span>';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"></span>',
                                Url::to(['/admin/cart/order-product', 'order_id' => $model->id]));
                    },
                ],
            ],
        ],
    ]); ?>
</div>
