<?php

namespace dench\cart\widgets;

use Yii;
use yii\base\Widget;

class LiqPay extends Widget
{
    public $action = 'pay';

    public $amount;

    public $currency = 'USD';

    public $description = 'Description text';

    public $order_id;

    public $version = 3;

    public $language = null;

    public $result_url = null;

    public $server_url = null;

    public $paytypes = null;

    public $sandbox = null;

    public $verifycode = null;

    public function run()
    {
        $liqpay = new \LiqPay(
            Yii::$app->params['liqpay']['public_key'],
            Yii::$app->params['liqpay']['private_key']
        );

        $params = [
            'action'         => $this->action,
            'amount'         => $this->amount,
            'currency'       => $this->currency,
            'description'    => $this->description,
            'order_id'       => $this->order_id,
            'version'        => $this->version,
            'language'       => $this->language,
            'result_url'     => $this->result_url,
            'server_url'     => $this->server_url,
            'paytypes'       => $this->paytypes,
            'sandbox'        => $this->sandbox,
            'verifycode'     => $this->verifycode,
        ];

        $params = array_filter($params, function($element) {
            return !empty($element);
        });

        $html = $liqpay->cnb_form($params);

        return $html;
    }
}