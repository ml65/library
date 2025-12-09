<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Author;
use app\models\AuthorSubscription;
use app\services\SmsService;

/**
 * Форма подписки на автора
 */
class SubscribeForm extends Model
{
    public $author_id;
    public $phone;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'phone'], 'required'],
            ['phone', 'string', 'max' => 20],
            ['author_id', 'integer'],
            ['author_id', 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'author_id' => 'Автор',
            'phone' => 'Телефон',
        ];
    }

    /**
     * Создать подписку на автора
     *
     * @return bool
     */
    public function subscribe()
    {
        if (!$this->validate()) {
            return false;
        }

        // Проверка на дубликат
        $exists = AuthorSubscription::find()
            ->where(['author_id' => $this->author_id, 'phone' => $this->phone])
            ->exists();

        if ($exists) {
            $this->addError('phone', 'Вы уже подписаны на этого автора');
            return false;
        }

        $subscription = new AuthorSubscription();
        $subscription->author_id = $this->author_id;
        $subscription->phone = $this->phone;

        if ($subscription->save()) {
            Yii::info("Subscription created: Author ID={$this->author_id}, Phone={$this->phone}", __METHOD__);
            
            // Отправить SMS подтверждение подписки
            $author = $subscription->author;
            if ($author) {
                SmsService::sendSubscriptionConfirmation($this->phone, $author->full_name);
            }
            
            return true;
        }

        // Если не удалось сохранить, добавить ошибки из модели
        if ($subscription->hasErrors()) {
            foreach ($subscription->getErrors() as $attribute => $errors) {
                foreach ($errors as $error) {
                    $this->addError($attribute, $error);
                }
            }
        }

        return false;
    }
}

