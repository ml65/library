<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AuthorSearch представляет модель для поиска и фильтрации авторов.
 */
class AuthorSearch extends Author
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['full_name'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Author::find();

        // Добавляем условия, которые всегда должны применяться здесь

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
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name]);

        return $dataProvider;
    }
}

