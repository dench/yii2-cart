<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order_product`.
 */
class m180120_155932_create_order_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('order_product', [
            'order_id' => $this->integer()->notNull(),
            'variant_id' => $this->integer(),
            'name' => $this->string(),
            'count' => $this->integer()->notNull(),
            'price' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk-order_product-order_id', 'order_product', 'order_id', 'order', 'id', 'CASCADE');

        $this->addForeignKey('fk-order_product-variant_id', 'order_product', 'variant_id', 'variant', 'id', 'SET NULL');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-order_product-variant_id', 'order_product');

        $this->dropForeignKey('fk-order_product-order_id', 'order_product');

        $this->dropTable('order_product');
    }
}
