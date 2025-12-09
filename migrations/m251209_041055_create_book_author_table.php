<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_author}}`.
 */
class m251209_041055_create_book_author_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%book_author}}', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ], $tableOptions);

        // Составной первичный ключ
        $this->addPrimaryKey('pk_book_author', '{{%book_author}}', ['book_id', 'author_id']);

        // Внешние ключи с CASCADE
        $this->addForeignKey(
            'fk_book_author_book',
            '{{%book_author}}',
            'book_id',
            '{{%book}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_book_author_author',
            '{{%book_author}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Индексы на внешние ключи
        $this->createIndex('idx-book_author-book_id', '{{%book_author}}', 'book_id');
        $this->createIndex('idx-book_author-author_id', '{{%book_author}}', 'author_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_book_author_book', '{{%book_author}}');
        $this->dropForeignKey('fk_book_author_author', '{{%book_author}}');
        $this->dropTable('{{%book_author}}');
    }
}
