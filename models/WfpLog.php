<?php

namespace dench\cart\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "yii_wfp_log".
 *
 * @property int $id
 * @property int $order_id
 * @property int $time
 * @property string $data
 * @property string $status
 *
 * @property string $statusName
 * @property string $statusColor
 */
class WfpLog extends ActiveRecord
{
    const STATUS_CREATED                = 'Created';
    const STATUS_IN_PROCESSING          = 'InProcessing';
    const STATUS_WAIT_AUTH_COMPLETE     = 'WaitingAuthComplete';
    const STATUS_APPROVED               = 'Approved';
    const STATUS_PENDING                = 'Pending';
    const STATUS_EXPIRED                = 'Expired';
    const STATUS_REFUNDED               = 'Refunded';
    const STATUS_VOIDED                 = 'Voided';
    const STATUS_DECLINED               = 'Declined';
    const STATUS_REFUND_IN_PROCESSING   = 'RefundInProcessing';

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
            [['order_id', 'status'], 'required'],
            [['status'], 'string', 'max' => 32],
            [['status'], 'in', 'range' => array_keys(static::statusList())],
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
            'status' => 'Status',
        ];
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return @self::statusList()[$this->status];
    }

    /**
     * @return string
     */
    public function getStatusColor()
    {
        return @self::statusColor()[$this->status];
    }

    /**
     * @return array
     */
    public static function statusList()
    {
        return [
            self::STATUS_CREATED => 'Создан',
            self::STATUS_IN_PROCESSING => Yii::t('app', 'В обработке'),
            self::STATUS_WAIT_AUTH_COMPLETE => 'Ожидает подтверждение списания средств',
            self::STATUS_APPROVED => Yii::t('app', 'Успешный платеж'),
            self::STATUS_PENDING => Yii::t('app', 'Платеж проверяется'),
            self::STATUS_EXPIRED => 'Истек срок оплаты',
            self::STATUS_REFUNDED => 'Возврат',
            self::STATUS_VOIDED => 'Возврат',
            self::STATUS_DECLINED => Yii::t('app', 'Платеж отклонен'),
            self::STATUS_REFUND_IN_PROCESSING => 'Возврат в обработке',
        ];
    }

    /**
     * @return array
     */
    public static function statusColor()
    {
        return [
            self::STATUS_CREATED => 'text-muted',
            self::STATUS_IN_PROCESSING => 'text-warning',
            self::STATUS_WAIT_AUTH_COMPLETE => 'text-muted',
            self::STATUS_APPROVED => 'text-success',
            self::STATUS_PENDING => 'text-warning',
            self::STATUS_EXPIRED => 'text-muted',
            self::STATUS_REFUNDED => 'text-muted',
            self::STATUS_VOIDED => 'text-muted',
            self::STATUS_DECLINED => 'text-danger',
            self::STATUS_REFUND_IN_PROCESSING => 'text-muted',
        ];
    }

    /**
     * @param $order_id
     * @param $data
     * @throws \Throwable
     */
    public static function log($order_id, $status, $data)
    {
        $log = (new self(['order_id' => $order_id, 'status' => $status, 'data' => json_encode($data)]));
        $log->insert();
    }
}
