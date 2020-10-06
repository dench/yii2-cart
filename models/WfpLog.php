<?php

namespace dench\cart\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "wfp_log".
 *
 * @property int $id
 * @property int $order_id
 * @property int $time
 * @property string $data
 */
class WfpLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wfp_log';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'time',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id'], 'integer'],
            [['order_id'], 'required'],
            [['data'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'time' => 'Time',
            'data' => 'Data',
        ];
    }

    /**
     * @param $order_id
     * @param $data
     * @throws \Throwable
     */
    public static function log($order_id, $data)
    {
        $log = (new self(['order_id' => $order_id, 'data' => json_encode($data)]));
        $log->insert();
    }
}
