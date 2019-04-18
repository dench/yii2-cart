<?php

use yii\db\Migration;

/**
 * Handles the creation of table `delivery`.
 */
class m190303_144119_create_delivery_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('delivery', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->notNull()->defaultValue(1),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('delivery_lang', [
            'delivery_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
            'text' => $this->text(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-delivery_lang', 'delivery_lang', ['delivery_id', 'lang_id']);

        $this->addForeignKey('fk-delivery_lang-delivery_id', 'delivery_lang', 'delivery_id', 'delivery', 'id', 'CASCADE');

        $this->addForeignKey('fk-delivery_lang-lang_id', 'delivery_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');

        $this->batchInsert('delivery', ['type', 'position'], [
            [2, 1],
            [3, 2],
            [4, 3],
        ]);

        $this->batchInsert('delivery_lang', ['delivery_id', 'lang_id', 'name'], [
            [1, 'ru', 'Самовывоз'],
            [1, 'uk', 'Самовивіз'],
            [2, 'ru', 'Новая почта'],
            [2, 'uk', 'Нова Пошта'],
            [3, 'ru', 'Доставка курьером'],
            [3, 'uk', 'Доставка кур\'єром'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //$this->dropPrimaryKey('pk-delivery_lang', 'delivery_lang');

        $this->dropForeignKey('fk-delivery_lang-delivery_id', 'delivery_lang');

        $this->dropForeignKey('fk-delivery_lang-lang_id', 'delivery_lang');

        $this->dropTable('delivery_lang');

        $this->dropTable('delivery');
    }
}
