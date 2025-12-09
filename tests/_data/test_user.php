<?php
// Создание тестового пользователя для тестов
use app\models\User;

$user = new User();
$user->username = 'admin';
$user->email = 'admin@test.com';
$user->setPassword('admin');
$user->generateAuthKey();
$user->status = User::STATUS_ACTIVE;
$user->save();

// Создаем пользователя demo для LoginFormTest
$demoUser = new User();
$demoUser->username = 'demo';
$demoUser->email = 'demo@test.com';
$demoUser->setPassword('demo');
$demoUser->generateAuthKey();
$demoUser->status = User::STATUS_ACTIVE;
$demoUser->save();

