<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BookSearch представляет модель для поиска и фильтрации книг.
 */
class BookSearch extends Book
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'year'], 'integer'],
            [['title', 'isbn', 'description'], 'safe'],
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

        return $dataProvider;
    }
}

