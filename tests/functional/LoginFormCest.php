<?php

class LoginFormCest
{
    public function _before(\FunctionalTester $I)
    {
        // Создать тестовых пользователей, если их нет
        if (!\app\models\User::findByUsername('admin')) {
            $user = new \app\models\User();
            $user->username = 'admin';
            $user->email = 'admin@test.com';
            $user->setPassword('admin');
            $user->generateAuthKey();
            $user->status = \app\models\User::STATUS_ACTIVE;
            $user->save();
        }
        
        if (!\app\models\User::findByUsername('demo')) {
            $demoUser = new \app\models\User();
            $demoUser->username = 'demo';
            $demoUser->email = 'demo@test.com';
            $demoUser->setPassword('demo');
            $demoUser->generateAuthKey();
            $demoUser->status = \app\models\User::STATUS_ACTIVE;
            $demoUser->save();
        }
        
        $I->amOnRoute('site/login');
    }

    public function openLoginPage(\FunctionalTester $I)
    {
        $I->see('Login', 'h1');

    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginById(\FunctionalTester $I)
    {
        $adminUser = \app\models\User::findByUsername('admin');
        verify($adminUser)->notEmpty();
        $I->amLoggedInAs($adminUser->id);
        $I->amOnPage('/');
        $I->see('Logout (admin)');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginByInstance(\FunctionalTester $I)
    {
        $adminUser = \app\models\User::findByUsername('admin');
        verify($adminUser)->notEmpty();
        $I->amLoggedInAs($adminUser);
        $I->amOnPage('/');
        $I->see('Logout (admin)');
    }

    public function loginWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validations errors');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');
    }

    public function loginWithWrongCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Incorrect username or password.');
    }

    public function loginSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'admin',
            'LoginForm[password]' => 'admin',
        ]);
        $I->see('Logout (admin)');
        $I->dontSeeElement('form#login-form');              
    }
}