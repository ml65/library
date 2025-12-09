<?php

namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Консольная команда для управления пользователями
 *
 * @author Your Name
 * @since 1.0
 */
class UserController extends Controller
{
    /**
     * Создание нового пользователя
     *
     * @param string $username Имя пользователя
     * @param string $email Email пользователя
     * @param string $password Пароль пользователя
     * @return int Exit code
     */
    public function actionCreate($username, $email, $password)
    {
        // Проверка существования пользователя
        if (User::findByUsername($username)) {
            $this->stdout("Пользователь с именем '{$username}' уже существует.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        if (User::find()->where(['email' => $email])->one()) {
            $this->stdout("Пользователь с email '{$email}' уже существует.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }

        // Создание нового пользователя
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;

        if ($user->save()) {
            $this->stdout("Пользователь '{$username}' успешно создан.\n", Console::FG_GREEN);
            $this->stdout("ID: {$user->id}\n", Console::FG_YELLOW);
            return ExitCode::OK;
        } else {
            $this->stdout("Ошибка при создании пользователя:\n", Console::FG_RED);
            foreach ($user->errors as $field => $errors) {
                foreach ($errors as $error) {
                    $this->stdout("  - {$field}: {$error}\n", Console::FG_RED);
                }
            }
            return ExitCode::DATAERR;
        }
    }

    /**
     * Удаление пользователя
     *
     * @param string $username Имя пользователя или ID
     * @return int Exit code
     */
    public function actionDelete($username)
    {
        $user = $this->findUser($username);
        if (!$user) {
            return ExitCode::DATAERR;
        }

        if ($user->delete()) {
            $this->stdout("Пользователь '{$user->username}' успешно удалён.\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stdout("Ошибка при удалении пользователя.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
    }

    /**
     * Изменение пароля пользователя
     *
     * @param string $username Имя пользователя или ID
     * @param string $password Новый пароль
     * @return int Exit code
     */
    public function actionChangePassword($username, $password)
    {
        $user = $this->findUser($username);
        if (!$user) {
            return ExitCode::DATAERR;
        }

        $user->setPassword($password);
        if ($user->save(false)) {
            $this->stdout("Пароль для пользователя '{$user->username}' успешно изменён.\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stdout("Ошибка при изменении пароля.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
    }

    /**
     * Активация пользователя
     *
     * @param string $username Имя пользователя или ID
     * @return int Exit code
     */
    public function actionActivate($username)
    {
        $user = $this->findUser($username);
        if (!$user) {
            return ExitCode::DATAERR;
        }

        $user->status = User::STATUS_ACTIVE;
        if ($user->save(false)) {
            $this->stdout("Пользователь '{$user->username}' активирован.\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stdout("Ошибка при активации пользователя.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
    }

    /**
     * Деактивация пользователя
     *
     * @param string $username Имя пользователя или ID
     * @return int Exit code
     */
    public function actionDeactivate($username)
    {
        $user = $this->findUser($username);
        if (!$user) {
            return ExitCode::DATAERR;
        }

        $user->status = User::STATUS_DELETED;
        if ($user->save(false)) {
            $this->stdout("Пользователь '{$user->username}' деактивирован.\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stdout("Ошибка при деактивации пользователя.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
    }

    /**
     * Список всех пользователей
     *
     * @return int Exit code
     */
    public function actionList()
    {
        $users = User::find()->all();

        if (empty($users)) {
            $this->stdout("Пользователи не найдены.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }

        $this->stdout("Список пользователей:\n\n", Console::FG_CYAN);
        $this->stdout(sprintf("%-5s %-20s %-30s %-10s %-20s\n", 
            "ID", "Username", "Email", "Status", "Created At"), Console::FG_YELLOW);
        $this->stdout(str_repeat("-", 85) . "\n");

        foreach ($users as $user) {
            $status = $user->status === User::STATUS_ACTIVE 
                ? Console::ansiFormat("Active", [Console::FG_GREEN])
                : Console::ansiFormat("Deleted", [Console::FG_RED]);
            
            $createdAt = date('Y-m-d H:i:s', $user->created_at);
            
            $this->stdout(sprintf("%-5d %-20s %-30s %-10s %-20s\n",
                $user->id,
                $user->username,
                $user->email,
                $status,
                $createdAt
            ));
        }

        return ExitCode::OK;
    }

    /**
     * Информация о пользователе
     *
     * @param string $username Имя пользователя или ID
     * @return int Exit code
     */
    public function actionInfo($username)
    {
        $user = $this->findUser($username);
        if (!$user) {
            return ExitCode::DATAERR;
        }

        $this->stdout("Информация о пользователе:\n\n", Console::FG_CYAN);
        $this->stdout("ID: {$user->id}\n", Console::FG_YELLOW);
        $this->stdout("Username: {$user->username}\n");
        $this->stdout("Email: {$user->email}\n");
        $this->stdout("Status: " . ($user->status === User::STATUS_ACTIVE ? "Active" : "Deleted") . "\n");
        $this->stdout("Created At: " . date('Y-m-d H:i:s', $user->created_at) . "\n");
        $this->stdout("Updated At: " . date('Y-m-d H:i:s', $user->updated_at) . "\n");

        return ExitCode::OK;
    }

    /**
     * Поиск пользователя по username или ID
     *
     * @param string $identifier Имя пользователя или ID
     * @return User|null
     */
    protected function findUser($identifier)
    {
        // Попытка найти по ID
        if (is_numeric($identifier)) {
            $user = User::findOne($identifier);
            if ($user) {
                return $user;
            }
        }

        // Поиск по username
        $user = User::find()->where(['username' => $identifier])->one();
        if ($user) {
            return $user;
        }

        // Поиск по email
        $user = User::find()->where(['email' => $identifier])->one();
        if ($user) {
            return $user;
        }

        $this->stdout("Пользователь '{$identifier}' не найден.\n", Console::FG_RED);
        return null;
    }
}

