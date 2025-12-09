<?php

namespace tests\unit\models;

use app\models\BookSearch;
use app\models\Book;

class BookSearchTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        // Очистить данные перед тестами
        Book::deleteAll();
    }

    public function testSearchByTitle()
    {
        // Создать тестовые книги
        $book1 = new Book();
        $book1->title = 'Война и мир';
        $book1->year = 1869;
        $book1->save();

        $book2 = new Book();
        $book2->title = 'Преступление и наказание';
        $book2->year = 1866;
        $book2->save();

        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(['BookSearch' => ['title' => 'Война']]);

        verify($dataProvider->getTotalCount())->equals(1);
        $models = $dataProvider->getModels();
        verify($models[0]->title)->equals('Война и мир');
    }

    public function testSearchByYear()
    {
        // Создать тестовые книги
        $book1 = new Book();
        $book1->title = 'Книга 1';
        $book1->year = 2020;
        $book1->save();

        $book2 = new Book();
        $book2->title = 'Книга 2';
        $book2->year = 2024;
        $book2->save();

        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(['BookSearch' => ['year' => 2024]]);

        verify($dataProvider->getTotalCount())->equals(1);
        $models = $dataProvider->getModels();
        verify($models[0]->year)->equals(2024);
    }

    public function testSearchByIsbn()
    {
        $book = new Book();
        $book->title = 'Тестовая Книга';
        $book->year = 2024;
        $book->isbn = '1234567890';
        $book->save();

        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(['BookSearch' => ['isbn' => '1234567890']]);

        verify($dataProvider->getTotalCount())->equals(1);
        $models = $dataProvider->getModels();
        verify($models[0]->isbn)->equals('1234567890');
    }

    public function testSearchEmpty()
    {
        // Создать тестовые книги
        $book1 = new Book();
        $book1->title = 'Книга 1';
        $book1->year = 2020;
        $book1->save();

        $book2 = new Book();
        $book2->title = 'Книга 2';
        $book2->year = 2024;
        $book2->save();

        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search([]);

        verify($dataProvider->getTotalCount())->equals(2);
    }
}

