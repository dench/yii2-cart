<?php

use yii\db\Migration;

/**
 * Handles adding delivery_id_payment_id to table `{{%order}}`.
 */
class m190316_153448_add_delivery_id_payment_id_columns_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order', 'delivery_id', $this->integer());

        $this->addColumn('order', 'payment_id', $this->integer());

        $this->addForeignKey('fk-order-delivery_id', 'order', 'delivery_id', 'delivery', 'id', 'SET NULL');

        $this->addForeignKey('fk-order-payment_id', 'order', 'payment_id', 'payment', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-order-delivery_id', 'order');

        $this->dropForeignKey('fk-order-payment_id', 'order');

        $this->dropColumn('order', 'delivery_id');

        $this->dropColumn('order', 'payment_id');
    }
}
