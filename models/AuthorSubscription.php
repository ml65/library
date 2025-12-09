<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Модель подписки на автора
 *
 * @property int $id
 * @property int $author_id ID автора
 * @property string $phone Номер телефона
 * @property int $created_at
 *
 * @property Author $author Автор
 */
class AuthorSubscription extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%author_subscription}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false, // Только created_at
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
            // Уникальность комбинации author_id и phone
            [['author_id', 'phone'], 'unique', 'targetAttribute' => ['author_id', 'phone']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Автор',
            'phone' => 'Телефон',
            'created_at' => 'Создано',
        ];
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

