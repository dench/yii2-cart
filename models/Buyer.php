<?php

namespace dench\cart\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "buyer".
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property int $created_at
 * @property boolean $entity
 * @property string $delivery
 *
 * @property Order[] $orders
 */
class Buyer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'buyer';
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone'], 'required'],
            [['entity'], 'boolean'],
            [['name', 'email', 'delivery'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Full Name'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'E-mail'),
            'created_at' => Yii::t('app', 'Created'),
            'entity' => Yii::t('app', 'Entity'),
            'delivery' => Yii::t('app', 'Delivery address'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['buyer_id' => 'id']);
    }

    public static function clearPhone($phone)
    {
        return preg_replace('/[^0-9]/','', $phone);
    }

    public function beforeValidate()
    {
        $this->phone = preg_replace('/[^0-9]/','', $this->phone);

        return parent::beforeValidate();
    }
}
