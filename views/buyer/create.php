<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model dench\cart\models\Buyer */

$this->title = Yii::t('app', 'Create Buyer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Buyers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="buyer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
