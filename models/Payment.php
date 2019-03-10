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
 */
class Payment extends ActiveRecord
{
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

    public static function getList($enabled = true)
    {
        $temp = self::find()->filterWhere(['enabled' => $enabled])->orderBy(['position' => SORT_ASC])->all();

        return ArrayHelper::map($temp, 'id', 'name');
    }
}
