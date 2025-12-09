<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm - модель формы обратной связи
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // name, email, subject и body обязательны
            [['name', 'email', 'subject', 'body'], 'required'],
            // email должен быть валидным адресом
            ['email', 'email'],
            // verifyCode должен быть введен правильно
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Код проверки',
        ];
    }

    /**
     * Отправляет email на указанный адрес используя информацию, собранную этой моделью.
     * @param string $email целевой email адрес
     * @return bool прошла ли модель валидацию
     */
    public function contact($email)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($this->body)
                ->send();

            return true;
        }
        return false;
    }
}
