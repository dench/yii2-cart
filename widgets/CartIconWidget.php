<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 20.01.18
 * Time: 14:02
 */

namespace app\widgets;

use dench\cart\models\Cart;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class CartIconWidget extends Widget
{
    public $id = 'cart-icon';

    public $options = [];

    public $urlCart = ['/cart/index'];

    public function run()
    {
        $cart = Cart::getCart();

        $options = [
            'id' => $this->id,
            'class' => 'cart-icon',
        ];

        $optionsClass = ArrayHelper::remove($this->options, 'class');

        $options = array_merge($options, $this->options);

        Html::addCssClass($options, $optionsClass);

        if ($count = count($cart)) {
            $count = '<span class="cart-count">' . $count . '</span>';
        } else {
            return Html::tag('span', null, $options);
        }

        return Html::a(
            Html::tag('i', null,
                ['class' => 'glyphicon glyphicon-shopping-cart']
            ) . $count, $this->urlCart, $options);
    }

    private function registerClientScript()
    {
        $url = Url::to('/cart/block');

        $js = <<< JS
function reloadCart() {
    $.get('{$url}', function(data) {
        $('#{$this->id}').after(data).remove();
    });
}
JS;
        $this->view->registerJs($js);
    }
}