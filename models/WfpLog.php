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
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
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
            'time' => 'Время',
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
        $log = new self();

        $log->order_id = $order_id ? (int) $order_id : null;

        $log->data = $data ? print_r($data, true) : null;

        if (!$log->save()) {
            $log->order_id = null;
            $log->save();
        }
    }
}
