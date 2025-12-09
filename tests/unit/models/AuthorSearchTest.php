<?php

namespace tests\unit\models;

use app\models\AuthorSearch;
use app\models\Author;

class AuthorSearchTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        // Очистить данные перед тестами
        Author::deleteAll();
    }

    public function testSearchByFullName()
    {
        // Создать тестовых авторов
        $author1 = new Author();
        $author1->full_name = 'Лев Толстой';
        $author1->save();

        $author2 = new Author();
        $author2->full_name = 'Федор Достоевский';
        $author2->save();

        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search(['AuthorSearch' => ['full_name' => 'Толстой']]);

        verify($dataProvider->getTotalCount())->equals(1);
        $models = $dataProvider->getModels();
        verify($models[0]->full_name)->equals('Лев Толстой');
    }

    public function testSearchEmpty()
    {
        // Создать тестовых авторов
        $author1 = new Author();
        $author1->full_name = 'Лев Толстой';
        $author1->save();

        $author2 = new Author();
        $author2->full_name = 'Федор Достоевский';
        $author2->save();

        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search([]);

        verify($dataProvider->getTotalCount())->equals(2);
    }

    public function testSearchByNonExistentName()
    {
        $author = new Author();
        $author->full_name = 'Лев Толстой';
        $author->save();

        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search(['AuthorSearch' => ['full_name' => 'Несуществующий']]);

        verify($dataProvider->getTotalCount())->equals(0);
    }
}

