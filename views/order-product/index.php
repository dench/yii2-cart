<?php

use dench\cart\models\Delivery;
use dench\cart\models\Order;
use dench\cart\models\Payment;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $searchModel dench\cart\models\OrderProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $order Order */

$this->title = Yii::t('app', 'Order #{order_id}', ['order_id' => $order->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders'), 'url' => ['order/index']];
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
var total = 0;
$('.grid-view .amount').each(function(){
    total += parseInt($(this).text());
});
$('.total').text(total);
JS;

$this->registerJs($js);
?>
<div class="order-product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Add product to order'), ['create', 'order_id' => $order->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'count',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'price',
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Amount'),
                'content' => function($data){
                    return $data->count * $data->price;
                },
                'contentOptions' => [
                    'class' => 'amount',
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>

    <div class="text-right">
        <?= Yii::t('app', 'Total amount') ?>: <span class="total h1"></span> грн
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group field-order-buyer_id required">
        <label class="control-label" for="order-buyer_id"><?= Yii::t('cart', 'Buyer') ?></label>
        <div>
            <?= $order->buyer->name ?> (<?= $order->buyer->entity ? Yii::t('app', 'Организация') : Yii::t('app', 'Частное лицо') ?>)
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/admin/cart/buyer/update', 'id' => $order->buyer->id]), ['target' => '_blank']) ?>
        </div>
    </div>

    <?= $form->field($order, 'amount')->textInput(['class' => 'form-control mw-250px']) ?>

    <?= $form->field($order, 'phone')->widget(MaskedInput::class, [
        'mask' => '+99 (999) 999-99-99',
        'options' => [
            'class' => 'form-control mw-250px',
        ],
    ]) ?>

    <?= $form->field($order, 'email')->textInput(['class' => 'form-control mw-250px']) ?>

    <?= $form->field($order, 'delivery_id')->dropDownList(Delivery::getList(), ['class' => 'form-control mw-250px']) ?>

    <?= $form->field($order, 'delivery')->textInput() ?>

    <?= $form->field($order, 'payment_id')->dropDownList(Payment::getList(), ['class' => 'form-control mw-250px']) ?>

    <?= $form->field($order, 'text')->textarea(['rows' => 6]) ?>

    <?= $form->field($order, 'status')->dropDownList(Order::statusList(), ['class' => 'form-control mw-250px']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
