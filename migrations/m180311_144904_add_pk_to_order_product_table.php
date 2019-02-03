<?php

use yii\db\Migration;

/**
 * Class m180311_144904_add_pk_to_order_product_table
 */
class m180311_144904_add_pk_to_order_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order_product', 'id', $this->primaryKey()->first());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order_product', 'id');
    }
}
