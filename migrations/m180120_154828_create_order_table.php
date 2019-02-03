<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m180120_154828_create_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'buyer_id' => $this->integer()->notNull(),
            'amount' => $this->integer()->notNull(),
            'text' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->addForeignKey('fk-order-buyer_id', 'order', 'buyer_id', 'buyer', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-order-buyer_id', 'order');

        $this->dropTable('order');
    }
}
