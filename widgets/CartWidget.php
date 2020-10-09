<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 20.01.18
 * Time: 14:02
 */

namespace dench\cart\widgets;

use dench\cart\models\Cart;
use dench\products\models\Variant;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class CartWidget extends Widget
{
    public $id = 'cart';

    public $options = [];

    public $urlCart = ['/cart/index'];

    public function run()
    {
        $cart = Cart::getCart();
        $variant_ids = array_keys($cart);
        $items = Variant::find()->where(['id' => $variant_ids])->andWhere(['enabled' => true])->all();

        if (empty($items)) {
            return Html::tag('div', '', ['id' => $this->id]);
        }

        $this->registerClientScript();

        $a_head = Html::a(Yii::t('app', 'Items in Cart'), $this->urlCart, ['class' => 'text-white', 'data-modal' => '/cart/modal']);

        $a_icon = Html::a(Html::tag('i', '', ['class' => 'fa fa-shopping-cart fa-2x fa-y pull-right']), $this->urlCart, ['data-modal' => '/cart/modal']);

        $cols[] = Html::tag('th', $a_head . $a_icon, ['class' => 'bg-gradient-secondary rounded-top border-0', 'colspan' => 2]);

        $tr[] = Html::tag('tr', implode("\n", $cols));

        $sum = 0;

        /** @var $item Variant */
        foreach ($items as $item) {
            $cols = [
                Html::tag('td', Html::a($item->product->name . ($item->name ? ', ' . $item->name : null), ['/product/index', 'slug' => $item->product->slug])),
                Html::tag('td', Yii::t('app', '{0} pcs', $cart[$item->id]), ['class' => 'text-right text-nowrap']),
            ];
            $tr[] = Html::tag('tr', implode("\n", $cols), ['class' => 'border']);

            $sum += $cart[$item->id] * $item->priceDef;
        }

        $tr[] = Html::tag('tr', Html::tag('td',
            '<b>' . Yii::t('app', 'Amount: {0} grn', $sum) . '</b>'
            . Html::a(Yii::t('app', 'Place an order'), ['/cart/index'], ['class' => 'btn btn-warning btn-block btn-lg mt-3']),
            [
                'class' => 'border',
                'colspan' => 2,
            ]
        ));

        $tbody = Html::tag('tbody', implode("\n", $tr));

        $options = [
            'id' => $this->id,
            'class' => 'table table-default bg-white',
        ];

        $optionsClass = ArrayHelper::remove($this->options, 'class');

        $options = array_merge($options, $this->options);

        Html::addCssClass($options, $optionsClass);

        return Html::tag('table', $tbody, $options);
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
        //$this->view->registerJs($js);
    }
}