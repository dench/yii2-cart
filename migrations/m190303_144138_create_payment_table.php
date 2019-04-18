<?php

use yii\db\Migration;

/**
 * Handles the creation of table `payment`.
 */
class m190303_144138_create_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('payment', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->notNull()->defaultValue(1),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions);
        
        $this->createTable('payment_lang', [
            'payment_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
            'text' => $this->text(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-payment_lang', 'payment_lang', ['payment_id', 'lang_id']);

        $this->addForeignKey('fk-payment_lang-payment_id', 'payment_lang', 'payment_id', 'payment', 'id', 'CASCADE');

        $this->addForeignKey('fk-payment_lang-lang_id', 'payment_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');

        $this->batchInsert('payment', ['type', 'position'], [
            [2, 1],
            [3, 2],
            [2, 3],
            [1, 4],
            [4, 5],
            [5, 6],
        ]);

        $this->batchInsert('payment_lang', ['payment_id', 'lang_id', 'name'], [
            [1, 'ru', 'Наличными при получении'],
            [1, 'uk', 'Готівкою при отриманні'],
            [2, 'ru', 'Оплата картой'],
            [2, 'uk', 'Оплата карткою'],
            [3, 'ru', 'Наложенный платеж'],
            [3, 'uk', 'Накладений платіж'],
            [4, 'ru', 'Оплата на карту ПриватБанка'],
            [4, 'uk', 'Оплата на картку ПриватБанку'],
            [5, 'ru', 'Оплата частями'],
            [5, 'uk', 'Оплата частинами'],
            [6, 'ru', 'Рассрочка'],
            [6, 'uk', 'Розстрочка'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //$this->dropPrimaryKey('pk-payment_lang', 'payment_lang');

        $this->dropForeignKey('fk-payment_lang-payment_id', 'payment_lang');

        $this->dropForeignKey('fk-payment_lang-lang_id', 'payment_lang');

        $this->dropTable('payment_lang');
        
        $this->dropTable('payment');
    }
}
