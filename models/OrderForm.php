<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 21.01.18
 * Time: 13:33
 */

namespace dench\cart\models;

use dench\products\models\Variant;
use himiklab\yii2\recaptcha\ReCaptchaValidator;
use Yii;
use yii\base\Model;
use yii\helpers\Url;

class OrderForm extends Model
{
    public $name;
    public $phone;
    public $email;
    public $delivery_id;
    public $delivery;
    public $payment_id;
    public $entity;
    public $reCaptcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone'], 'required'],
            [['delivery_id', 'payment_id'], 'integer'],
            [['name', 'phone', 'email', 'delivery'], 'string'],
            ['email', 'email'],
            [['entity'], 'boolean'],
            ['reCaptcha', ReCaptchaValidator::class, 'skipOnEmpty' => YII_DEBUG ? true : false, 'uncheckedMessage' => Yii::t('app', 'Пожалуйста, подтвердите, что вы не бот.')],
        ];
    }

    public function scenarios()
    {
        return [
            'admin' => ['name', 'phone', 'email', 'delivery_id', 'delivery', 'payment_id', 'entity'],
            'user' => ['name', 'phone', 'email', 'delivery_id', 'delivery', 'payment_id', 'entity', 'reCaptcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Full Name'),
            'phone' => Yii::t('app', 'Contact phone'),
            'email' => Yii::t('app', 'Your E-mail'),
            'delivery_id' => Yii::t('app', 'Choose the appropriate delivery method'),
            'delivery' => Yii::t('app', 'Delivery address'),
            'payment_id' => Yii::t('app', 'Select the appropriate method of payment'),
            'entity' => Yii::t('app', 'Buyer is'),
        ];
    }

    public function send()
    {
        $this->phone = Buyer::clearPhone($this->phone);

        $buyer = Buyer::findOne(['phone' => $this->phone]);

        if (empty($buyer)) {
            $buyer = new Buyer();
        }

        $buyer->name = ($this->name && $buyer->name != $this->name) ? $this->name : $buyer->name;
        $buyer->phone = ($this->phone && $buyer->phone != $this->phone) ? $this->phone : $buyer->phone;
        $buyer->delivery = ($this->delivery && $buyer->delivery != $this->delivery) ? $this->delivery : $buyer->delivery;
        $buyer->email = ($this->email && $buyer->email != $this->email) ? $this->email : $buyer->email;
        $buyer->entity = ($this->entity != null && $buyer->entity != $this->entity) ? $this->entity : $buyer->entity;

        if ($buyer->save()) {

            $cart = Cart::getCart();

            $product_ids = [];
            $amount = 0;

            $cartItemName = [];
            $cartItemCount = [];
            $cartItemPrice = [];

            foreach ($cart as $k => $v) {
                /** @var $item Variant */
                $item = Variant::find()->where(['id' => $k, 'enabled' => true])->one();
                if ($item) {
                    $cartItemName[$k] = $item->product->name . ', ' . $item->name;
                    $cartItemCount[$k] = $v;
                    $cartItemPrice[$k] = $item->priceDef;
                    $product_ids[] = $item->id;
                    $amount += $v * $item->priceDef;
                }
            }

            $status = Order::STATUS_NEW;

            $awaiting = Yii::$app->params['liqpay']['status_awaiting'];

            if (in_array($this->payment_id, $awaiting)) {
                $status = Order::STATUS_AWAITING;
            }

            $order = new Order([
                'buyer_id' => $buyer->id,
                'product_ids' => $product_ids,
                'amount' => $amount,
                'phone' => $this->phone,
                'email' => $this->email,
                'delivery' => $this->delivery,
                'delivery_id' => $this->delivery_id,
                'payment_id' => $this->payment_id,
                'status' => $status,
            ]);

            $order->cartItemName = $cartItemName;
            $order->cartItemCount = $cartItemCount;
            $order->cartItemPrice = $cartItemPrice;

            if ($order->save()) {
                Cart::clearCart();

                Yii::$app->mailer->compose()
                    ->setTo(Yii::$app->params['adminEmail'])
                    ->setSubject('Заказ № ' . $order->id)
                    ->setTextBody(Url::to(['/admin/cart/order-product', 'order_id' => $order->id], 'https'))
                    ->send();

                return $order->id;
            }
        }

        return false;
    }
}