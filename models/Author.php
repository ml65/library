<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Модель автора
 *
 * @property int $id
 * @property string $full_name ФИО автора
 *
 * @property Book[] $books Книги автора
 */
class Author extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%author}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name'], 'required'],
            [['full_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'full_name' => 'ФИО',
        ];
    }

    /**
     * Получить книги автора
     * Связь многие-ко-многим через промежуточную таблицу book_author
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('{{%book_author}}', ['author_id' => 'id']);
    }
}

