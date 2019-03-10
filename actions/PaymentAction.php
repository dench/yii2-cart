<?php

namespace dench\cart\actions;

use dench\cart\models\Payment;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class PaymentAction extends Action
{
    public function runWithParams($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = [];

        if ($temp = Payment::find()->where(['id' => $id])->one()) {
            $data = ArrayHelper::toArray($temp);
            $data['name'] = $temp->name;
            $data['text'] = $temp->text;
        }

        return $data;
    }
}
