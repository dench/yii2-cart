<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model dench\cart\models\OrderProduct */

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders'), 'url' => ['/admin/cart/order/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Order #{order_id}', ['order_id' => $model->order_id]), 'url' => ['index', 'order_id' => $model->order_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="order-product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
