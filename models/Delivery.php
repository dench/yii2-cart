<?php

namespace dench\cart\models;

use dench\language\behaviors\LanguageBehavior;
use dench\sortable\behaviors\SortableBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "delivery".
 *
 * @property int $id
 * @property int $type
 * @property bool $enabled
 */
class Delivery extends ActiveRecord
{
    const TYPE_UNDEFINED = 1;
    const TYPE_PICKUP = 2;
    const TYPE_ADDRESS = 3;
    const TYPE_DEPARTMENT = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivery';
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
            [['type'], 'in', 'range' => [self::TYPE_UNDEFINED, self::TYPE_PICKUP, self::TYPE_ADDRESS, self::TYPE_DEPARTMENT]],
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
            self::TYPE_PICKUP => Yii::t('cart', 'Pickup'),
            self::TYPE_ADDRESS => Yii::t('cart', 'Delivery address'),
            self::TYPE_DEPARTMENT => Yii::t('cart', 'Delivery department'),
        ];
    }
    
    public static function getList($enabled = true)
    {
        $temp = self::find()->filterWhere(['enabled' => $enabled])->orderBy(['position' => SORT_ASC])->all();

        return ArrayHelper::map($temp, 'id', 'name');
    }
}
