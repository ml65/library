<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%author}}`.
 */
class m251209_041054_create_author_table extends Migration
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

        $this->createTable('{{%author}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string(255)->notNull(),
        ], $tableOptions);

        // Индекс на full_name для поиска
        $this->createIndex('idx-author-full_name', '{{%author}}', 'full_name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%author}}');
    }
}
