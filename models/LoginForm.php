<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm - модель формы входа в систему
 *
 * @property-read User|null $user
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username и password обязательны
            [['username', 'password'], 'required'],
            // rememberMe должен быть булевым значением
            ['rememberMe', 'boolean'],
            // password валидируется методом validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Валидация пароля.
     * Этот метод служит встроенной валидацией для пароля.
     *
     * @param string $attribute атрибут, который сейчас валидируется
     * @param array $params дополнительные пары имя-значение, переданные в правиле
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверное имя пользователя или пароль.');
            }
        }
    }

    /**
     * Выполняет вход пользователя используя предоставленные имя пользователя и пароль.
     * @return bool успешно ли выполнен вход пользователя
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Находит пользователя по [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
