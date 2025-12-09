<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Промежуточная модель связи книга-автор
 *
 * @property int $book_id
 * @property int $author_id
 *
 * @property Book $book
 * @property Author $author
 */
class BookAuthor extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%book_author}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['book_id', 'author_id'], 'required'],
            [['book_id', 'author_id'], 'integer'],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'book_id' => 'Книга',
            'author_id' => 'Автор',
        ];
    }

    /**
     * Получить книгу
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    /**
     * Получить автора
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}

