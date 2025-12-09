<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%author_subscription}}`.
 */
class m251209_041056_create_author_subscription_table extends Migration
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

        $this->createTable('{{%author_subscription}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'phone' => $this->string(20)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // Внешний ключ с CASCADE
        $this->addForeignKey(
            'fk_author_subscription_author',
            '{{%author_subscription}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // UNIQUE KEY для предотвращения дубликатов подписки
        $this->createIndex('idx-author_subscription-author_id', '{{%author_subscription}}', 'author_id');
        $this->createIndex('idx-author_subscription-phone', '{{%author_subscription}}', 'phone');
        
        // Составной уникальный ключ (author_id, phone)
        $this->createIndex('idx-author_subscription-unique', '{{%author_subscription}}', ['author_id', 'phone'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_author_subscription_author', '{{%author_subscription}}');
        $this->dropTable('{{%author_subscription}}');
    }
}
