<?php

namespace tests\unit\models;

use app\models\Author;
use app\models\Book;
use app\models\BookAuthor;

class AuthorTest extends \Codeception\Test\Unit
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
        $author = new Author();
        verify($author->validate())->false();
        verify($author->hasErrors('full_name'))->true();
    }

    public function testValidationMaxLength()
    {
        $author = new Author();
        $author->full_name = str_repeat('a', 256); // Превышает максимум
        verify($author->validate())->false();
        verify($author->hasErrors('full_name'))->true();
    }

    public function testSaveAuthor()
    {
        $author = new Author();
        $author->full_name = 'Тестовый Автор';
        verify($author->save())->true();
        verify($author->id)->notEmpty();
    }

    public function testGetBooksRelation()
    {
        $author = new Author();
        $author->full_name = 'Автор с книгами';
        $author->save();

        $book1 = new Book();
        $book1->title = 'Книга 1';
        $book1->year = 2024;
        $book1->save();

        $book2 = new Book();
        $book2->title = 'Книга 2';
        $book2->year = 2024;
        $book2->save();

        // Создать связи
        $bookAuthor1 = new BookAuthor();
        $bookAuthor1->book_id = $book1->id;
        $bookAuthor1->author_id = $author->id;
        $bookAuthor1->save();

        $bookAuthor2 = new BookAuthor();
        $bookAuthor2->book_id = $book2->id;
        $bookAuthor2->author_id = $author->id;
        $bookAuthor2->save();

        // Проверить связь
        $author = Author::findOne($author->id);
        $books = $author->books;
        verify(count($books))->equals(2);
        verify($books[0]->title)->equals('Книга 1');
        verify($books[1]->title)->equals('Книга 2');
    }

    public function testTableName()
    {
        verify(Author::tableName())->equals('{{%author}}');
    }
}

