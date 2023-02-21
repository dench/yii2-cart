<?php

namespace dench\cart\models;

use dench\image\helpers\ImageHelper;
use dench\products\models\Variant;
use voskobovich\linker\LinkerBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $buyer_id
 * @property int $amount
 * @property string $text
 * @property int $created_at
 * @property int $status
 * @property array $product_ids
 * @property string $phone
 * @property string $email
 * @property string $delivery
 * @property int $delivery_id
 * @property int $payment_id
 * @property string $comment
 *
 * @property Buyer $buyer
 * @property Variant[] $products
 * @property OrderProduct[] $orderProducts
 * @property Delivery $deliveryMethod
 * @property Payment $paymentMethod
 */
class Order extends ActiveRecord
{
    public $cartItemName = [];
    public $cartItemCount = [];
    public $cartItemPrice = [];

    const STATUS_NEW = 1;
    const STATUS_VIEWED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_AWAITING = 5;
    const STATUS_PAID = 6;
    const STATUS_ERROR = 7;

    public function init()
    {
        $this->cartItemName = $this->getCartItemName();
        $this->cartItemCount = $this->getCartItemCount();
        $this->cartItemPrice = $this->getCartItemPrice();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
            [
                'class' => LinkerBehavior::class,
                'relations' => [
                    'product_ids' => [
                        'products',
                        'updater' => [
                            'viaTableAttributesValue' => [
                                'name' => function($updater, $relatedPk, $rowCondition) {
                                    $primaryModel = $updater->getBehavior()->owner;
                                    return @$primaryModel->cartItemName[$relatedPk];
                                },
                                'count' => function($updater, $relatedPk, $rowCondition) {
                                    $primaryModel = $updater->getBehavior()->owner;
                                    return @$primaryModel->cartItemCount[$relatedPk];
                                },
                                'price' => function($updater, $relatedPk, $rowCondition) {
                                    $primaryModel = $updater->getBehavior()->owner;
                                    return @$primaryModel->cartItemPrice[$relatedPk];
                                },
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['buyer_id', 'amount', 'product_ids', 'phone'], 'required'],
            [['buyer_id', 'amount', 'status', 'delivery_id', 'payment_id'], 'integer'],
            [['text'], 'string'],
            [['email', 'delivery'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 20],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_NEW, self::STATUS_VIEWED, self::STATUS_COMPLETED, self::STATUS_CANCELED, self::STATUS_AWAITING, self::STATUS_PAID, self::STATUS_ERROR]],
            [['product_ids'], 'each', 'rule' => ['integer']],
            [['buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Buyer::class, 'targetAttribute' => ['buyer_id' => 'id']],
            [['delivery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Delivery::class, 'targetAttribute' => ['delivery_id' => 'id']],
            [['payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payment::class, 'targetAttribute' => ['payment_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'buyer_id' => Yii::t('cart', 'Buyer'),
            'amount' => Yii::t('cart', 'Amount'),
            'text' => Yii::t('cart', 'Manager\'s comment'),
            'created_at' => Yii::t('cart', 'Created'),
            'status' => Yii::t('cart', 'Status'),
            'phone' => Yii::t('cart', 'Phone'),
            'email' => Yii::t('cart', 'E-mail'),
            'delivery' => Yii::t('cart', 'Delivery address'),
            'delivery_id' => Yii::t('cart', 'Delivery method'),
            'payment_id' => Yii::t('cart', 'Payment method'),
            'comment' => Yii::t('cart', 'Comments to the order'),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset(Yii::$app->queue)
            && isset(Yii::$app->params['rememberReviewDelay'])
            && !$insert
            && isset($changedAttributes['status'])
            && !empty($this->email)
            && $this->status == self::STATUS_COMPLETED
            && $changedAttributes['status'] !== self::STATUS_COMPLETED) {
                $products = [];
            foreach ($this->products as $product) {
                $products[] = [
                    'imageUrl' => $product->image ? Url::to(ImageHelper::thumb($product->image->id, 'micro'), 'https') : null,
                    'url' => Url::to(['/product/index', 'slug' => $product->product->slug], 'https'),
                    'name' => $this->cartItemName[$product->id],
                    'cost' => $this->cartItemPrice[$product->id],
                    'quantity' => $this->cartItemCount[$product->id],
                ];
            }
                Yii::$app->queue->delay(Yii::$app->params['rememberReviewDelay'])->push(new \app\jobs\RememberReviewJob([
                    'email' => $this->email,
                    'products' => $products,
                ]));
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuyer()
    {
        return $this->hasOne(Buyer::class, ['id' => 'buyer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Variant::class, ['id' => 'variant_id'])->viaTable('order_product', ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProduct::class, ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeliveryMethod()
    {
        return $this->hasOne(Delivery::class, ['id' => 'delivery_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(Payment::class, ['id' => 'payment_id']);
    }

    public static function unread()
    {
        return self::find()->where(['status' => [self::STATUS_NEW, self::STATUS_AWAITING]])->count();
    }

    public static function read($id = null)
    {
        if ($order = self::findOne($id)) {
            if ($order->status === Order::STATUS_NEW) {
                $order->status = self::STATUS_VIEWED;
                $order->save();
            }
        }
    }

    public function getCartItemName()
    {
        return OrderProduct::find()->select(['name'])->indexBy('variant_id')->asArray()->column();
    }

    public function getCartItemCount()
    {
        return OrderProduct::find()->select(['count'])->indexBy('variant_id')->asArray()->column();
    }

    public function getCartItemPrice()
    {
        return OrderProduct::find()->select(['price'])->indexBy('variant_id')->asArray()->column();
    }

    public static function statusList()
    {
        return [
            self::STATUS_NEW => Yii::t('cart', 'New'),
            self::STATUS_VIEWED => Yii::t('cart', 'Processing'),
            self::STATUS_COMPLETED => Yii::t('cart', 'Completed'),
            self::STATUS_CANCELED => Yii::t('cart', 'Cancelled'),
            self::STATUS_AWAITING => Yii::t('cart', 'Awaiting payment'),
            self::STATUS_PAID => Yii::t('cart', 'Paid'),
            self::STATUS_ERROR => Yii::t('cart', 'Not paid'),
        ];
    }

    public static function statusClass()
    {
        return [
            self::STATUS_NEW => 'danger',
            self::STATUS_VIEWED => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELED => 'default',
            self::STATUS_AWAITING => 'info',
            self::STATUS_PAID => 'primary',
            self::STATUS_ERROR => 'danger',
        ];
    }

    public function beforeValidate()
    {
        $this->phone = preg_replace('/[^0-9]/','', $this->phone);

        return parent::beforeValidate();
    }
}
