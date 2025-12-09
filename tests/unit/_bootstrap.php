<?php

// add unit testing specific bootstrap code here
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/test.php';
new yii\console\Application($config);

// Создаем тестовых пользователей, если их нет
use app\models\User;

if (!User::findByUsername('admin')) {
    $user = new User();
    $user->username = 'admin';
    $user->email = 'admin@test.com';
    $user->setPassword('admin');
    $user->generateAuthKey();
    $user->status = User::STATUS_ACTIVE;
    $user->save();
}

if (!User::findByUsername('demo')) {
    $demoUser = new User();
    $demoUser->username = 'demo';
    $demoUser->email = 'demo@test.com';
    $demoUser->setPassword('demo');
    $demoUser->generateAuthKey();
    $demoUser->status = User::STATUS_ACTIVE;
    $demoUser->save();
}
