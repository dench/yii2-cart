<?php

use kartik\typeahead\Typeahead;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model dench\cart\models\OrderProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->widget(Typeahead::class, [
        'options' => ['placeholder' => Yii::t('app', 'Product name ...')],
        'pluginOptions' => ['highlight' => true],
        'dataset' => [
            [
                'prefetch' => Url::to(['product-list', 'rand' => rand(100000,999999)]),
                'display' => 'value',
                'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                'limit' => 10,
                'templates' => [
                    'notFound' => '<div class="text-danger" style="padding:0 8px">' . Yii::t('app', 'Not found') . '</div>',
                ]
            ]
        ],
        'pluginEvents' => [
            'typeahead:select' => "function(ev, suggestion) { $('#orderproduct-variant_id').val(suggestion.id); $('#orderproduct-price').val(suggestion.price); }",
        ],
    ]) ?>

    <?= Html::activeHiddenInput($model, 'variant_id') ?>

    <?= $form->field($model, 'count')->textInput(['class' => 'form-control mw-250px']) ?>

    <?= $form->field($model, 'price')->textInput(['class' => 'form-control mw-250px']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
