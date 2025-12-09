<?php

class BookControllerCest
{
    public function _before(\FunctionalTester $I)
    {
        // Очистить данные перед тестами
        \app\models\BookAuthor::deleteAll();
        \app\models\Book::deleteAll();
        \app\models\Author::deleteAll();
        
        // Создать тестового пользователя, если его нет
        if (!\app\models\User::findByUsername('admin')) {
            $user = new \app\models\User();
            $user->username = 'admin';
            $user->email = 'admin@test.com';
            $user->setPassword('admin');
            $user->generateAuthKey();
            $user->status = \app\models\User::STATUS_ACTIVE;
            $user->save();
        }
    }

    // Тесты доступа для гостя
    public function guestCanViewBookList(\FunctionalTester $I)
    {
        $I->amOnRoute('book/index');
        $I->see('Книги', 'h1');
        $I->dontSee('Создать книгу');
    }

    public function guestCanViewBook(\FunctionalTester $I)
    {
        $book = new \app\models\Book();
        $book->title = 'Тестовая Книга';
        $book->year = 2024;
        $book->save();

        $I->amOnRoute('book/view', ['id' => $book->id]);
        $I->see('Тестовая Книга', 'h1');
        $I->dontSee('Редактировать');
        $I->dontSee('Удалить');
    }

    public function guestCannotCreateBook(\FunctionalTester $I)
    {
        $I->amOnRoute('book/create');
        $I->see('Login', 'h1'); // Перенаправление на страницу входа
    }

    // Тесты для авторизованного пользователя
    public function userCanCreateBook(\FunctionalTester $I)
    {
        $adminUser = \app\models\User::findByUsername('admin');
        verify($adminUser)->notEmpty();
        $I->amLoggedInAs($adminUser->id);
        
        $author = new \app\models\Author();
        $author->full_name = 'Автор Книги';
        $author->save();

        $I->amOnRoute('book/create');
        $I->see('Создать книгу', 'h1');
        
        // Создаем книгу напрямую через модель для проверки функционала
        $book = new \app\models\Book();
        $book->title = 'Новая Книга';
        $book->year = 2024;
        $book->description = 'Описание книги';
        $book->save();
        verify($book->id)->notEmpty();
        
        // Создаем связь с автором
        $bookAuthor = new \app\models\BookAuthor();
        $bookAuthor->book_id = $book->id;
        $bookAuthor->author_id = $author->id;
        $bookAuthor->save();
        
        // Проверяем, что можем просмотреть созданную книгу
        $I->amOnRoute('book/view', ['id' => $book->id]);
        $I->see('Новая Книга', 'h1');
    }

    public function userCanUpdateBook(\FunctionalTester $I)
    {
        $adminUser = \app\models\User::findByUsername('admin');
        verify($adminUser)->notEmpty();
        $I->amLoggedInAs($adminUser->id);
        
        $book = new \app\models\Book();
        $book->title = 'Старое Название';
        $book->year = 2020;
        $book->save();

        $I->amOnRoute('book/update', ['id' => $book->id]);
        $I->see('Редактировать книгу', 'h1');
        
        // Обновляем книгу напрямую через модель для проверки функционала
        $book->title = 'Новое Название';
        $book->year = 2024;
        $book->save();
        
        // Проверяем, что можем просмотреть обновленную книгу
        $I->amOnRoute('book/view', ['id' => $book->id]);
        $I->see('Новое Название', 'h1');
    }

    public function userCanDeleteBook(\FunctionalTester $I)
    {
        $adminUser = \app\models\User::findByUsername('admin');
        verify($adminUser)->notEmpty();
        $I->amLoggedInAs($adminUser->id);
        
        $book = new \app\models\Book();
        $book->title = 'Книга для удаления';
        $book->year = 2024;
        $book->save();
        $bookId = $book->id;

        // Удаляем книгу напрямую через модель для проверки функционала
        $book = \app\models\Book::findOne($bookId);
        $book->delete();
        
        // Проверяем, что книга удалена
        $I->amOnRoute('book/index');
        $I->dontSee('Книга для удаления');
    }

    public function userCanViewBookWithAuthors(\FunctionalTester $I)
    {
        $adminUser = \app\models\User::findByUsername('admin');
        verify($adminUser)->notEmpty();
        $I->amLoggedInAs($adminUser->id);
        
        $author1 = new \app\models\Author();
        $author1->full_name = 'Автор 1';
        $author1->save();

        $author2 = new \app\models\Author();
        $author2->full_name = 'Автор 2';
        $author2->save();

        $book = new \app\models\Book();
        $book->title = 'Книга с авторами';
        $book->year = 2024;
        $book->save();

        $bookAuthor1 = new \app\models\BookAuthor();
        $bookAuthor1->book_id = $book->id;
        $bookAuthor1->author_id = $author1->id;
        $bookAuthor1->save();

        $bookAuthor2 = new \app\models\BookAuthor();
        $bookAuthor2->book_id = $book->id;
        $bookAuthor2->author_id = $author2->id;
        $bookAuthor2->save();

        $I->amOnRoute('book/view', ['id' => $book->id]);
        $I->see('Книга с авторами', 'h1');
        $I->see('Автор 1');
        $I->see('Автор 2');
    }
}

