<?php

use dench\cart\models\Delivery;
use dench\language\models\Language;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model dench\cart\models\Delivery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="delivery-form">

    <?php $form = ActiveForm::begin(); ?>

    <ul class="nav nav-tabs">
        <li class="nav-item active"><a href="#tab-main" class="nav-link" data-toggle="tab"><?= Yii::t('cart', 'Main') ?></a></li>
        <?php foreach (Language::suffixList() as $suffix => $name) : ?>
            <li class="nav-item"><a href="#lang<?= $suffix ?>" class="nav-link" data-toggle="tab"><?= $name ?></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content">

        <div class="tab-pane active" id="tab-main">
            <?= $form->field($model, 'type')->dropDownList(Delivery::typeList()) ?>

            <?= $form->field($model, 'enabled')->checkbox() ?>
        </div>

        <?php foreach (Language::suffixList() as $suffix => $name) : ?>
            <div class="tab-pane fade" id="lang<?= $suffix ?>">
                <?= $form->field($model, 'name' . $suffix)->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'text' . $suffix)->widget(CKEditor::class, [
                    'preset' => 'full',
                    'options' => [
                        'id' => 'pagetext' . $suffix,
                    ],
                    'clientOptions' => [
                        'customConfig' => '/js/ckeditor.js',
                        'language' => Yii::$app->language,
                        'allowedContent' => true,
                    ]
                ]) ?>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
