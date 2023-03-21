<?php
/**
 * Created by PhpStorm.
 * User: dench
 * Date: 21.01.18
 * Time: 13:33
 */

namespace dench\cart\models;

use dench\products\models\Variant;
use himiklab\yii2\recaptcha\ReCaptchaValidator2;
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
    public $comment;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone'], 'required'],
            [['delivery_id', 'payment_id'], 'integer'],
            [['name', 'phone', 'email', 'delivery', 'comment'], 'string'],
            ['email', 'email'],
            [['entity'], 'boolean'],
            ['reCaptcha', ReCaptchaValidator2::class, 'skipOnEmpty' => YII_DEBUG ? true : false, 'uncheckedMessage' => Yii::t('cart', 'Please confirm that you are not a bot.')],
        ];
    }

    public function scenarios()
    {
        return [
            'admin' => ['name', 'phone', 'email', 'delivery_id', 'delivery', 'payment_id', 'entity', 'comment'],
            'user' => ['name', 'phone', 'email', 'delivery_id', 'delivery', 'payment_id', 'entity', 'comment', 'reCaptcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('cart', 'Full Name'),
            'phone' => Yii::t('cart', 'Contact phone'),
            'email' => Yii::t('cart', 'Your E-mail'),
            'delivery_id' => Yii::t('cart', 'Choose the appropriate delivery method'),
            'delivery' => Yii::t('cart', 'Delivery address'),
            'payment_id' => Yii::t('cart', 'Select the appropriate method of payment'),
            'entity' => Yii::t('cart', 'Buyer is'),
            'comment' => Yii::t('cart', 'Comments to the order'),
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
                    $cartItemName[$k] = $item->product->name . ($item->name ? ', ' . $item->name : null);
                    $cartItemCount[$k] = $v;
                    $cartItemPrice[$k] = $item->priceDef;
                    $product_ids[] = $item->id;
                    $amount += $v * $item->priceDef;
                }
            }

            $status = Order::STATUS_NEW;

            $payments = Payment::find()->select('id')->where(['type' => [Payment::TYPE_LIQPAY, Payment::TYPE_WFP]])->column();

            if (in_array($this->payment_id, $payments)) {
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
                'comment' => $this->comment,
            ]);

            $order->cartItemName = $cartItemName;
            $order->cartItemCount = $cartItemCount;
            $order->cartItemPrice = $cartItemPrice;

            if ($order->save()) {
                Cart::clearCart();

                Yii::$app->queue->push(new \dench\cart\jobs\EmailJob([
                    'emailFrom' => [isset(Yii::$app->params['fromEmail']) ? Yii::$app->params['fromEmail'] : Yii::$app->params['adminEmail'] => Yii::$app->name],
                    'emailTo' => isset(Yii::$app->params['toEmail']) ? Yii::$app->params['toEmail'] : Yii::$app->params['adminEmail'],
                    'subject' => 'Заказ № ' . $order->id,
                    'body' => Url::to(['/admin/cart/order-product', 'order_id' => $order->id], 'https'),
                ]));

                return $order->id;
            }
        }

        return false;
    }
}