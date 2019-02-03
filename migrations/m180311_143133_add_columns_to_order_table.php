<?php

use yii\db\Migration;

/**
 * Class m180311_143133_add_columns_to_order_table
 */
class m180311_143133_add_columns_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order', 'phone', 'string(12) not null');
        $this->addColumn('order', 'email', 'string');
        $this->addColumn('order', 'delivery', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'phone');
        $this->dropColumn('order', 'email');
        $this->dropColumn('order', 'delivery');
    }
}
