<?php

namespace dench\cart\models;

use dench\products\models\Variant;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_product".
 *
 * @property int $order_id
 * @property int $variant_id
 * @property string $name
 * @property int $count
 * @property int $price
 *
 * @property Order $order
 * @property Variant $variant
 */
class OrderProduct extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'count', 'price'], 'required'],
            [['order_id', 'variant_id', 'count', 'price'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['variant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Variant::class, 'targetAttribute' => ['variant_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'variant_id' => 'Variant ID',
            'name' => Yii::t('cart', 'Name'),
            'count' => Yii::t('cart', 'Count'),
            'price' => Yii::t('cart', 'Price'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariant()
    {
        return $this->hasOne(Variant::class, ['id' => 'variant_id']);
    }
}
