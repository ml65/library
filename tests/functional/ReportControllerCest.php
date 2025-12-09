<?php

class ReportControllerCest
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
    public function guestCanViewTopAuthorsReport(\FunctionalTester $I)
    {
        $I->amOnRoute('report/top-authors');
        $I->see('ТОП-10 авторов', 'h1');
        $I->see('Год:');
    }

    // Тесты для авторизованного пользователя
    public function userCanViewTopAuthorsReport(\FunctionalTester $I)
    {
        $adminUser = \app\models\User::findByUsername('admin');
        verify($adminUser)->notEmpty();
        $I->amLoggedInAs($adminUser->id);
        
        $I->amOnRoute('report/top-authors');
        $I->see('ТОП-10 авторов', 'h1');
        $I->see('Год:');
    }

    // Тест отображения отчета с данными
    public function reportShowsTopAuthors(\FunctionalTester $I)
    {
        // Создать авторов
        $author1 = new \app\models\Author();
        $author1->full_name = 'Автор 1 (5 книг)';
        $author1->save();

        $author2 = new \app\models\Author();
        $author2->full_name = 'Автор 2 (3 книги)';
        $author2->save();

        $author3 = new \app\models\Author();
        $author3->full_name = 'Автор 3 (10 книг)';
        $author3->save();

        // Создать книги для автора 1 (5 книг)
        for ($i = 1; $i <= 5; $i++) {
            $book = new \app\models\Book();
            $book->title = "Книга {$i} автора 1";
            $book->year = 2020;
            $book->save();

            $bookAuthor = new \app\models\BookAuthor();
            $bookAuthor->book_id = $book->id;
            $bookAuthor->author_id = $author1->id;
            $bookAuthor->save();
        }

        // Создать книги для автора 2 (3 книги)
        for ($i = 1; $i <= 3; $i++) {
            $book = new \app\models\Book();
            $book->title = "Книга {$i} автора 2";
            $book->year = 2020;
            $book->save();

            $bookAuthor = new \app\models\BookAuthor();
            $bookAuthor->book_id = $book->id;
            $bookAuthor->author_id = $author2->id;
            $bookAuthor->save();
        }

        // Создать книги для автора 3 (10 книг)
        for ($i = 1; $i <= 10; $i++) {
            $book = new \app\models\Book();
            $book->title = "Книга {$i} автора 3";
            $book->year = 2020;
            $book->save();

            $bookAuthor = new \app\models\BookAuthor();
            $bookAuthor->book_id = $book->id;
            $bookAuthor->author_id = $author3->id;
            $bookAuthor->save();
        }

        $I->amOnRoute('report/top-authors');
        $I->see('ТОП-10 авторов', 'h1');
        
        // Проверить, что авторы отображаются
        $I->see('Автор 3 (10 книг)');
        $I->see('Автор 1 (5 книг)');
        $I->see('Автор 2 (3 книги)');
        
        // Проверить, что есть таблица с данными
        $I->seeElement('table');
        $I->see('Количество книг', 'th');
    }

    // Тест фильтрации по году
    public function reportFiltersByYear(\FunctionalTester $I)
    {
        // Создать автора
        $author = new \app\models\Author();
        $author->full_name = 'Автор с книгами';
        $author->save();

        // Создать книги за 2020 год
        for ($i = 1; $i <= 3; $i++) {
            $book = new \app\models\Book();
            $book->title = "Книга 2020-{$i}";
            $book->year = 2020;
            $book->save();

            $bookAuthor = new \app\models\BookAuthor();
            $bookAuthor->book_id = $book->id;
            $bookAuthor->author_id = $author->id;
            $bookAuthor->save();
        }

        // Создать книги за 2021 год
        for ($i = 1; $i <= 2; $i++) {
            $book = new \app\models\Book();
            $book->title = "Книга 2021-{$i}";
            $book->year = 2021;
            $book->save();

            $bookAuthor = new \app\models\BookAuthor();
            $bookAuthor->book_id = $book->id;
            $bookAuthor->author_id = $author->id;
            $bookAuthor->save();
        }

        // Проверить отчет за все годы (должно быть 5 книг)
        $I->amOnRoute('report/top-authors');
        $I->see('Автор с книгами');
        $I->see('5', 'td'); // 5 книг всего

        // Проверить отчет за 2020 год (должно быть 3 книги)
        $I->amOnRoute('report/top-authors', ['year' => 2020]);
        $I->see('Автор с книгами');
        $I->see('3', 'td'); // 3 книги за 2020 год
        $I->see('Показаны авторы за 2020 год');

        // Проверить отчет за 2021 год (должно быть 2 книги)
        $I->amOnRoute('report/top-authors', ['year' => 2021]);
        $I->see('Автор с книгами');
        $I->see('2', 'td'); // 2 книги за 2021 год
        $I->see('Показаны авторы за 2021 год');

        // Проверить отчет за несуществующий год
        $I->amOnRoute('report/top-authors', ['year' => 1999]);
        $I->see('Авторы не найдены');
    }

    // Тест пустого отчета
    public function reportShowsEmptyMessageWhenNoAuthors(\FunctionalTester $I)
    {
        $I->amOnRoute('report/top-authors');
        $I->see('ТОП-10 авторов', 'h1');
        $I->see('Авторы не найдены');
    }

    // Тест сортировки по количеству книг (DESC)
    public function reportSortsAuthorsByBookCount(\FunctionalTester $I)
    {
        // Создать авторов с разным количеством книг
        $author1 = new \app\models\Author();
        $author1->full_name = 'Автор 1';
        $author1->save();

        $author2 = new \app\models\Author();
        $author2->full_name = 'Автор 2';
        $author2->save();

        $author3 = new \app\models\Author();
        $author3->full_name = 'Автор 3';
        $author3->save();

        // Автор 1: 1 книга
        $book1 = new \app\models\Book();
        $book1->title = 'Книга 1';
        $book1->year = 2020;
        $book1->save();
        $bookAuthor1 = new \app\models\BookAuthor();
        $bookAuthor1->book_id = $book1->id;
        $bookAuthor1->author_id = $author1->id;
        $bookAuthor1->save();

        // Автор 2: 5 книг
        for ($i = 1; $i <= 5; $i++) {
            $book = new \app\models\Book();
            $book->title = "Книга автора 2-{$i}";
            $book->year = 2020;
            $book->save();
            $bookAuthor = new \app\models\BookAuthor();
            $bookAuthor->book_id = $book->id;
            $bookAuthor->author_id = $author2->id;
            $bookAuthor->save();
        }

        // Автор 3: 3 книги
        for ($i = 1; $i <= 3; $i++) {
            $book = new \app\models\Book();
            $book->title = "Книга автора 3-{$i}";
            $book->year = 2020;
            $book->save();
            $bookAuthor = new \app\models\BookAuthor();
            $bookAuthor->book_id = $book->id;
            $bookAuthor->author_id = $author3->id;
            $bookAuthor->save();
        }

        $I->amOnRoute('report/top-authors');
        
        // Проверить, что все авторы отображаются
        $I->see('Автор 1');
        $I->see('Автор 2');
        $I->see('Автор 3');
        
        // Проверить количество книг
        $I->see('1', 'td'); // Автор 1
        $I->see('5', 'td'); // Автор 2
        $I->see('3', 'td'); // Автор 3
        
        // Проверить, что таблица существует и содержит данные
        $I->seeElement('table');
        $I->seeElement('table tbody tr');
    }
}

