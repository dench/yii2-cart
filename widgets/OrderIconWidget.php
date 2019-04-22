<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 22.04.18
 * Time: 10:01
 */

namespace dench\cart\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class OrderIconWidget extends Widget
{
    public $id = 'order-icon';

    public $options = [];

    public $urlOrder = ['/order/index'];

    public function run()
    {
        $order_id = Yii::$app->request->cookies->getValue('order');

        $options = [
            'id' => $this->id,
            'class' => 'order-icon',
        ];

        $optionsClass = ArrayHelper::remove($this->options, 'class');

        $options = array_merge($options, $this->options);

        Html::addCssClass($options, $optionsClass);

        if ($order_id) {
            $count = '<span class="order-count">1</span>';
        } else {
            return null;
        }

        $this->urlOrder['id'] = $order_id;

        $this->urlOrder['hash'] = md5($order_id . Yii::$app->params['order_secret']);

        return Html::a(
            Html::tag('i', null,
                ['class' => 'glyphicon glyphicon glyphicon-list-alt']
            ) . $count, $this->urlOrder, $options);
    }

}