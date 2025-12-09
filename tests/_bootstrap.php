<?php

// Подключаем автозагрузчик
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Загружаем конфигурацию для тестов
$config = require __DIR__ . '/../config/test.php';
new yii\console\Application($config);

// Применяем миграции к тестовой БД
$migration = new \yii\console\controllers\MigrateController('migrate', Yii::$app);
$migration->runAction('up', ['interactive' => false]);
