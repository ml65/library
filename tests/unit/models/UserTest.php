<?php

namespace tests\unit\models;

use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        // Создаем тестового пользователя, если его нет
        if (!User::findByUsername('admin')) {
            $user = new User();
            $user->username = 'admin';
            $user->email = 'admin@test.com';
            $user->setPassword('admin');
            $user->generateAuthKey();
            $user->status = User::STATUS_ACTIVE;
            $user->save();
        }
    }

    public function testFindUserById()
    {
        // Найти пользователя admin
        $adminUser = User::findByUsername('admin');
        verify($adminUser)->notEmpty();
        
        // Проверить поиск по ID
        verify($user = User::findIdentity($adminUser->id))->notEmpty();
        verify($user->username)->equals('admin');

        verify(User::findIdentity(999))->empty();
    }

    public function testFindUserByUsername()
    {
        verify($user = User::findByUsername('admin'))->notEmpty();
        verify(User::findByUsername('not-admin'))->empty();
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser()
    {
        $user = User::findByUsername('admin');
        verify($user)->notEmpty();
        
        // Проверить валидацию auth_key
        verify($user->validateAuthKey($user->auth_key))->notEmpty();
        verify($user->validateAuthKey('wrong-key'))->empty();

        // Проверить валидацию пароля
        verify($user->validatePassword('admin'))->notEmpty();
        verify($user->validatePassword('wrong-password'))->empty();        
    }

}
