<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * Модель книги
 *
 * @property int $id
 * @property string $title Название
 * @property int|null $year Год издания
 * @property string|null $description Описание
 * @property string|null $isbn ISBN
 * @property string|null $cover_path Путь к обложке
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Author[] $authors Авторы книги
 * 
 * @property UploadedFile $coverFile Файл обложки
 * @property array $authorIds Массив ID авторов
 */
class Book extends ActiveRecord
{
    /**
     * @var UploadedFile Файл обложки
     */
    public $coverFile;

    /**
     * @var array Массив ID авторов для формы
     */
    public $authorIds = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%book}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => 2100],
            [['description'], 'string'],
            [['title', 'isbn', 'cover_path'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 20],
            [['authorIds'], 'safe'],
            [
                'coverFile',
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'jpg, jpeg, png',
                'maxSize' => Yii::$app->params['book']['maxCoverSize'] ?? 5 * 1024 * 1024,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'year' => 'Год издания',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_path' => 'Обложка',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Получить авторов книги
     * Связь многие-ко-многим через промежуточную таблицу book_author
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }

    /**
     * Загрузить файл обложки
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate() && $this->coverFile) {
            $fileName = uniqid() . '.' . $this->coverFile->extension;
            $path = Yii::getAlias('@webroot/uploads/covers/') . $fileName;
            if ($this->coverFile->saveAs($path)) {
                $this->cover_path = 'uploads/covers/' . $fileName;
                return true;
            }
        }
        return false;
    }

    /**
     * После загрузки модели заполнить authorIds
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->authorIds = array_column($this->authors, 'id');
    }
}

