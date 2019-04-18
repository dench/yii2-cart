<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%liqpay_log}}`.
 */
class m190415_112200_create_liqpay_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('liqpay_log', [
            'id' => $this->primaryKey(),
            'time' => $this->integer()->notNull(),
            'order_id' => $this->integer(),
            'data' => $this->text(),
        ], $tableOptions);

        $this->addForeignKey('fk-liqpay_log-order_id', 'liqpay_log', 'order_id', 'order', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-liqpay_log-order_id', 'liqpay_log');

        $this->dropTable('liqpay_log');
    }
}
