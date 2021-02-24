<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model dench\cart\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">
<div class="alert alert-danger"><?= Yii::t('app', 'Не работает') ?></div>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['placeholder' => Yii::t('app', 'Фамилия Имя Отчество')]) ?>

    <?= $form->field($model, 'phone')->widget(MaskedInput::class, [
        'mask' => '+99 (999) 999-99-99',
    ]) ?>

    <?= $form->field($model, 'delivery')->textInput(['placeholder' => Yii::t('app', 'Введите город и номер отделения Новой почты')]) ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'entity')->radioList([
        0 => Yii::t('app', 'Частное лицо'),
        1 => Yii::t('app', 'Организация'),
    ], ['class' => 'pt-2']) ?>

    <?= $form->field($model, 'comment')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
