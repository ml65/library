<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Контроллер для инициализации RBAC
 * 
 * Использование: yii rbac/init
 */
class RbacController extends Controller
{
    /**
     * Инициализация RBAC: создание ролей и permissions
     * 
     * @return int Exit code
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // Удалить старые данные (если есть)
        $auth->removeAll();

        // Создать permissions
        $manageBooks = $auth->createPermission('manageBooks');
        $manageBooks->description = 'Управление книгами';
        $auth->add($manageBooks);

        $manageAuthors = $auth->createPermission('manageAuthors');
        $manageAuthors->description = 'Управление авторами';
        $auth->add($manageAuthors);

        // Создать роль user
        $user = $auth->createRole('user');
        $user->description = 'Авторизованный пользователь';
        $auth->add($user);

        // Назначить permissions роли user
        $auth->addChild($user, $manageBooks);
        $auth->addChild($user, $manageAuthors);

        $this->stdout("RBAC initialized successfully.\n");
        $this->stdout("Created role: user\n");
        $this->stdout("Created permissions: manageBooks, manageAuthors\n");
        $this->stdout("Permissions assigned to role 'user'.\n");

        return ExitCode::OK;
    }
}

