<?php

namespace dench\cart\actions;

use dench\cart\models\Payment;
use Yii;
use yii\base\Action;

class PaymentAction extends Action
{
    public function run()
    {
        $id = Yii::$app->request->post('id');

        if ($model = Payment::findOne($id)) {
            return $this->controller->renderAjax('payment', [
                'model' => $model,
            ]);
        } else {
            return null;
        }
    }
}
