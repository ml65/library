<?php

namespace tests\unit\models;

use app\models\BookAuthor;
use app\models\Book;
use app\models\Author;

class BookAuthorTest extends \Codeception\Test\Unit
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
        $bookAuthor = new BookAuthor();
        verify($bookAuthor->validate())->false();
        verify($bookAuthor->hasErrors('book_id'))->true();
        verify($bookAuthor->hasErrors('author_id'))->true();
    }

    public function testSaveBookAuthor()
    {
        $book = new Book();
        $book->title = 'Тестовая Книга';
        $book->year = 2024;
        $book->save();

        $author = new Author();
        $author->full_name = 'Тестовый Автор';
        $author->save();

        $bookAuthor = new BookAuthor();
        $bookAuthor->book_id = $book->id;
        $bookAuthor->author_id = $author->id;
        verify($bookAuthor->save())->true();
    }

    public function testGetBookRelation()
    {
        $book = new Book();
        $book->title = 'Книга для связи';
        $book->year = 2024;
        $book->save();

        $author = new Author();
        $author->full_name = 'Автор для связи';
        $author->save();

        $bookAuthor = new BookAuthor();
        $bookAuthor->book_id = $book->id;
        $bookAuthor->author_id = $author->id;
        $bookAuthor->save();

        // Проверить связь
        $bookAuthor = BookAuthor::findOne(['book_id' => $book->id, 'author_id' => $author->id]);
        $relatedBook = $bookAuthor->book;
        verify($relatedBook)->notEmpty();
        verify($relatedBook->title)->equals('Книга для связи');
    }

    public function testGetAuthorRelation()
    {
        $book = new Book();
        $book->title = 'Книга для связи';
        $book->year = 2024;
        $book->save();

        $author = new Author();
        $author->full_name = 'Автор для связи';
        $author->save();

        $bookAuthor = new BookAuthor();
        $bookAuthor->book_id = $book->id;
        $bookAuthor->author_id = $author->id;
        $bookAuthor->save();

        // Проверить связь
        $bookAuthor = BookAuthor::findOne(['book_id' => $book->id, 'author_id' => $author->id]);
        $relatedAuthor = $bookAuthor->author;
        verify($relatedAuthor)->notEmpty();
        verify($relatedAuthor->full_name)->equals('Автор для связи');
    }

    public function testTableName()
    {
        verify(BookAuthor::tableName())->equals('{{%book_author}}');
    }
}

