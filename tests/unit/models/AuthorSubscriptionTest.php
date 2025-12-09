<?php

namespace tests\unit\models;

use app\models\AuthorSubscription;
use app\models\Author;

class AuthorSubscriptionTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        // Очистить данные перед тестами
        AuthorSubscription::deleteAll();
        Author::deleteAll();
    }

    public function testValidationRequiredFields()
    {
        $subscription = new AuthorSubscription();
        verify($subscription->validate())->false();
        verify($subscription->hasErrors('author_id'))->true();
        verify($subscription->hasErrors('phone'))->true();
    }

    public function testSaveSubscription()
    {
        $author = new Author();
        $author->full_name = 'Тестовый Автор';
        $author->save();

        $subscription = new AuthorSubscription();
        $subscription->author_id = $author->id;
        $subscription->phone = '+79001234567';
        verify($subscription->save())->true();
        verify($subscription->id)->notEmpty();
        verify($subscription->created_at)->notEmpty();
    }

    public function testUniqueConstraint()
    {
        $author = new Author();
        $author->full_name = 'Тестовый Автор';
        $author->save();

        // Создать первую подписку
        $subscription1 = new AuthorSubscription();
        $subscription1->author_id = $author->id;
        $subscription1->phone = '+79001234567';
        verify($subscription1->save())->true();

        // Попытка создать дубликат
        $subscription2 = new AuthorSubscription();
        $subscription2->author_id = $author->id;
        $subscription2->phone = '+79001234567';
        verify($subscription2->save())->false();
        verify($subscription2->hasErrors())->true();
    }

    public function testGetAuthorRelation()
    {
        $author = new Author();
        $author->full_name = 'Автор для подписки';
        $author->save();

        $subscription = new AuthorSubscription();
        $subscription->author_id = $author->id;
        $subscription->phone = '+79001234567';
        $subscription->save();

        // Проверить связь
        $subscription = AuthorSubscription::findOne($subscription->id);
        $relatedAuthor = $subscription->author;
        verify($relatedAuthor)->notEmpty();
        verify($relatedAuthor->full_name)->equals('Автор для подписки');
    }

    public function testTimestampBehavior()
    {
        $author = new Author();
        $author->full_name = 'Тестовый Автор';
        $author->save();

        $subscription = new AuthorSubscription();
        $subscription->author_id = $author->id;
        $subscription->phone = '+79001234567';
        $subscription->save();

        verify($subscription->created_at)->notEmpty();
        verify($subscription->created_at)->greaterThan(0);
    }

    public function testTableName()
    {
        verify(AuthorSubscription::tableName())->equals('{{%author_subscription}}');
    }
}

