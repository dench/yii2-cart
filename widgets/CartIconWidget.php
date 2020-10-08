<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 20.01.18
 * Time: 14:02
 */

namespace dench\cart\widgets;

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

    public $iconOptions = ['class' => 'glyphicon glyphicon-shopping-cart'];

    public function run()
    {
        $cart = Cart::getCart();

        $this->registerClientScript();

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
                $this->iconOptions
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