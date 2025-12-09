<?php

namespace tests\unit\models;

use app\models\Book;
use app\models\Author;
use app\models\BookAuthor;
use Yii;

class BookTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        // Очистить данные перед тестами
        BookAuthor::deleteAll();
        Book::deleteAll();
        Author::deleteAll();
    }

    public function testValidationRequiredFields()
    {
        $book = new Book();
        verify($book->validate())->false();
        verify($book->hasErrors('title'))->true();
    }

    public function testValidationYearRange()
    {
        $book = new Book();
        $book->title = 'Тест';
        
        // Год меньше минимума
        $book->year = 500;
        verify($book->validate())->false();
        verify($book->hasErrors('year'))->true();

        // Год больше максимума
        $book->year = 3000;
        verify($book->validate())->false();
        verify($book->hasErrors('year'))->true();

        // Валидный год
        $book->year = 2024;
        verify($book->validate())->true();
    }

    public function testSaveBook()
    {
        $book = new Book();
        $book->title = 'Тестовая Книга';
        $book->year = 2024;
        verify($book->save())->true();
        verify($book->id)->notEmpty();
        verify($book->created_at)->notEmpty();
        verify($book->updated_at)->notEmpty();
    }

    public function testGetAuthorsRelation()
    {
        $book = new Book();
        $book->title = 'Книга с авторами';
        $book->year = 2024;
        $book->save();

        $author1 = new Author();
        $author1->full_name = 'Автор 1';
        $author1->save();

        $author2 = new Author();
        $author2->full_name = 'Автор 2';
        $author2->save();

        // Создать связи
        $bookAuthor1 = new BookAuthor();
        $bookAuthor1->book_id = $book->id;
        $bookAuthor1->author_id = $author1->id;
        $bookAuthor1->save();

        $bookAuthor2 = new BookAuthor();
        $bookAuthor2->book_id = $book->id;
        $bookAuthor2->author_id = $author2->id;
        $bookAuthor2->save();

        // Проверить связь
        $book = Book::findOne($book->id);
        $authors = $book->authors;
        verify(count($authors))->equals(2);
        verify($authors[0]->full_name)->equals('Автор 1');
        verify($authors[1]->full_name)->equals('Автор 2');
    }

    public function testAfterFindLoadsAuthorIds()
    {
        $book = new Book();
        $book->title = 'Книга';
        $book->year = 2024;
        $book->save();

        $author1 = new Author();
        $author1->full_name = 'Автор 1';
        $author1->save();

        $author2 = new Author();
        $author2->full_name = 'Автор 2';
        $author2->save();

        // Создать связи
        $bookAuthor1 = new BookAuthor();
        $bookAuthor1->book_id = $book->id;
        $bookAuthor1->author_id = $author1->id;
        $bookAuthor1->save();

        $bookAuthor2 = new BookAuthor();
        $bookAuthor2->book_id = $book->id;
        $bookAuthor2->author_id = $author2->id;
        $bookAuthor2->save();

        // Проверить, что authorIds загружается после find
        $book = Book::findOne($book->id);
        verify(count($book->authorIds))->equals(2);
        verify(in_array($author1->id, $book->authorIds))->true();
        verify(in_array($author2->id, $book->authorIds))->true();
    }

    public function testTableName()
    {
        verify(Book::tableName())->equals('{{%book}}');
    }
}

