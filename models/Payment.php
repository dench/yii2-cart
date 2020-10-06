<?php

namespace dench\cart\models;

use dench\language\behaviors\LanguageBehavior;
use dench\sortable\behaviors\SortableBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property bool $enabled
 * @property string $name
 * @property string $text
 * @property integer $type
 */
class Payment extends ActiveRecord
{
    const TYPE_UNDEFINED = 1;
    const TYPE_CASH = 2;
    const TYPE_LIQPAY = 3;
    const TYPE_PARTS = 4;
    const TYPE_PLAN = 5;
    const TYPE_WFP = 6;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            LanguageBehavior::class,
            SortableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['type'], 'default', 'value' => self::TYPE_UNDEFINED],
            [['type'], 'in', 'range' => [self::TYPE_UNDEFINED, self::TYPE_CASH, self::TYPE_LIQPAY, self::TYPE_PARTS, self::TYPE_PLAN, self::TYPE_WFP]],
            [['enabled'], 'boolean'],
            [['enabled'], 'default', 'value' => true],
            [['name'], 'string', 'max' => 255],
            [['text'], 'string'],
            [['name', 'text'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'enabled' => Yii::t('app', 'Enabled'),
            'name' => Yii::t('app', 'Name'),
            'text' => Yii::t('app', 'Text'),
        ];
    }

    /**
     * @return MultilingualQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    public static function typeList()
    {
        return [
            self::TYPE_UNDEFINED => Yii::t('cart', 'Undefined'),
            self::TYPE_CASH => Yii::t('cart', 'Cash on delivery'),
            self::TYPE_LIQPAY => Yii::t('cart', 'Liqpay'),
            self::TYPE_PARTS => Yii::t('cart', 'Payment in parts'),
            self::TYPE_PLAN => Yii::t('cart', 'Installment plan'),
            self::TYPE_WFP => Yii::t('cart', 'WayForPay'),
        ];
    }

    public static function getList($enabled = true)
    {
        $temp = self::find()->filterWhere(['enabled' => $enabled])->orderBy(['position' => SORT_ASC])->all();

        return ArrayHelper::map($temp, 'id', 'name');
    }
}
