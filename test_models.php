<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/console.php';
new yii\console\Application($config);

echo "=== Тестирование моделей и связей ===\n\n";

// Тест 1: Создание автора
echo "Тест 1: Создание автора\n";
$author = new \app\models\Author();
$author->full_name = 'Лев Толстой';
if ($author->save()) {
    echo "✓ Автор создан: ID={$author->id}, ФИО={$author->full_name}\n";
} else {
    echo "✗ Ошибка создания автора: " . print_r($author->errors, true) . "\n";
    exit(1);
}

// Тест 2: Создание книги
echo "\nТест 2: Создание книги\n";
$book = new \app\models\Book();
$book->title = 'Война и мир';
$book->year = 1869;
$book->description = 'Роман-эпопея';
if ($book->save()) {
    echo "✓ Книга создана: ID={$book->id}, Название={$book->title}\n";
} else {
    echo "✗ Ошибка создания книги: " . print_r($book->errors, true) . "\n";
    exit(1);
}

// Тест 3: Связь книга-автор через промежуточную таблицу
echo "\nТест 3: Создание связи книга-автор\n";
$bookAuthor = new \app\models\BookAuthor();
$bookAuthor->book_id = $book->id;
$bookAuthor->author_id = $author->id;
if ($bookAuthor->save()) {
    echo "✓ Связь книга-автор создана\n";
} else {
    echo "✗ Ошибка создания связи: " . print_r($bookAuthor->errors, true) . "\n";
    exit(1);
}

// Тест 4: Проверка связи Author->Books
echo "\nТест 4: Проверка связи Author->Books\n";
$author = \app\models\Author::findOne($author->id);
$books = $author->books;
echo "✓ Автор '{$author->full_name}' имеет книг: " . count($books) . "\n";
if (count($books) > 0) {
    echo "  - Книга: {$books[0]->title}\n";
} else {
    echo "✗ Ошибка: связь не работает\n";
    exit(1);
}

// Тест 5: Проверка связи Book->Authors
echo "\nТест 5: Проверка связи Book->Authors\n";
$book = \app\models\Book::findOne($book->id);
$authors = $book->authors;
echo "✓ Книга '{$book->title}' имеет авторов: " . count($authors) . "\n";
if (count($authors) > 0) {
    echo "  - Автор: {$authors[0]->full_name}\n";
} else {
    echo "✗ Ошибка: связь не работает\n";
    exit(1);
}

// Тест 6: Создание подписки
echo "\nТест 6: Создание подписки\n";
$subscription = new \app\models\AuthorSubscription();
$subscription->author_id = $author->id;
$subscription->phone = '+79001234567';
if ($subscription->save()) {
    echo "✓ Подписка создана: ID={$subscription->id}, Телефон={$subscription->phone}\n";
} else {
    echo "✗ Ошибка создания подписки: " . print_r($subscription->errors, true) . "\n";
    exit(1);
}

// Тест 7: Проверка связи AuthorSubscription->Author
echo "\nТест 7: Проверка связи AuthorSubscription->Author\n";
$subscription = \app\models\AuthorSubscription::findOne($subscription->id);
$author = $subscription->author;
if ($author) {
    echo "✓ Подписка связана с автором: {$author->full_name}\n";
} else {
    echo "✗ Ошибка: связь не работает\n";
    exit(1);
}

// Тест 8: Валидация - попытка создать дубликат подписки
echo "\nТест 8: Валидация дубликата подписки\n";
$duplicate = new \app\models\AuthorSubscription();
$duplicate->author_id = $author->id;
$duplicate->phone = '+79001234567';
if (!$duplicate->save()) {
    echo "✓ Валидация работает: дубликат подписки отклонен\n";
} else {
    echo "✗ Ошибка: дубликат подписки был создан\n";
    exit(1);
}

// Тест 9: Валидация года книги
echo "\nТест 9: Валидация года книги\n";
$invalidBook = new \app\models\Book();
$invalidBook->title = 'Тест';
$invalidBook->year = 500; // Невалидный год
if (!$invalidBook->save()) {
    echo "✓ Валидация года работает: год 500 отклонен\n";
} else {
    echo "✗ Ошибка: невалидный год был принят\n";
    exit(1);
}

// Тест 10: Проверка обязательных полей
echo "\nТест 10: Проверка обязательных полей\n";
$emptyAuthor = new \app\models\Author();
if (!$emptyAuthor->save()) {
    echo "✓ Валидация обязательных полей работает: пустой автор отклонен\n";
} else {
    echo "✗ Ошибка: пустой автор был создан\n";
    exit(1);
}

echo "\n=== Все тесты пройдены успешно! ===\n";

