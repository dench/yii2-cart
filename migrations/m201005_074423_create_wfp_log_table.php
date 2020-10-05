<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wfp_log}}`.
 */
class m201005_074423_create_wfp_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('{{%wfp_log}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'time' => $this->integer()->notNull(),
            'data' => $this->text(),
        ], $tableOptions);

        $this->addForeignKey('fk-wfp_log-order_id', '{{%wfp_log}}', 'order_id', '{{%order}}', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-wfp_log-order_id', '{{%wfp_log}}');

        $this->dropTable('{{%wfp_log}}');
    }
}
