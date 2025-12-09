<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BookSearch представляет модель для поиска и фильтрации книг.
 * 
 * @property string $searchQuery Поисковый запрос (по названию или автору)
 */
class BookSearch extends Book
{
    /**
     * @var string Поисковый запрос (по названию или автору)
     */
    public $searchQuery;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'year'], 'integer'],
            [['title', 'isbn', 'description', 'searchQuery'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // Обход scenarios() в родительском классе
        return Model::scenarios();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['searchQuery'] = 'Поиск';
        return $labels;
    }

    /**
     * Создает провайдер данных с применением поискового запроса
     *
     * @param array $params
     * @param string $viewMode Режим просмотра ('table' или 'cards')
     *
     * @return ActiveDataProvider
     */
    public function search($params, $viewMode = 'table')
    {
        $query = Book::find();

        // Оптимизация: загружать авторов для избежания N+1 запросов
        $query->with('authors');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Если валидация не прошла, вернуть пустой провайдер данных
            $query->where('0=1');
            return $dataProvider;
        }

        // Условия фильтрации
        $query->andFilterWhere([
            'id' => $this->id,
            'year' => $this->year,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'isbn', $this->isbn])
            ->andFilterWhere(['like', 'description', $this->description]);

        // Поиск по названию или автору (одна строка поиска)
        if (!empty($this->searchQuery)) {
            $query->joinWith('authors')
                ->andWhere([
                    'or',
                    ['like', '{{%book}}.title', $this->searchQuery],
                    ['like', Author::tableName() . '.full_name', $this->searchQuery]
                ])
                ->groupBy('{{%book}}.id'); // Избежать дубликатов при нескольких авторах
        }

        return $dataProvider;
    }
}

