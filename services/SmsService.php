<?php

namespace app\services;

use Yii;

/**
 * Сервис отправки SMS
 * 
 * Эмулятор: логирует SMS вместо реальной отправки
 */
class SmsService
{
    /**
     * Отправить SMS
     *
     * @param string $phone Номер телефона
     * @param string $text Текст сообщения
     * @return bool
     */
    public static function send($phone, $text)
    {
        $apiKey = Yii::$app->params['sms']['apiKey'] ?? 'emulator';

        // Эмулятор: только логирование
        if ($apiKey === 'emulator') {
            Yii::info("SMS to {$phone}: {$text}", __METHOD__);
            return true;
        }

        // В реальности здесь был бы запрос к SMSPilot API
        // Пример:
        // $client = new \GuzzleHttp\Client();
        // $response = $client->post('https://smspilot.ru/api.php', [
        //     'form_params' => [
        //         'send' => $text,
        //         'to' => $phone,
        //         'apikey' => $apiKey,
        //     ],
        // ]);
        // return $response->getStatusCode() === 200;

        return false;
    }

    /**
     * Отправить SMS подтверждение подписки на автора
     *
     * @param string $phone Номер телефона
     * @param string $authorName Имя автора
     * @return bool
     */
    public static function sendSubscriptionConfirmation($phone, $authorName)
    {
        $text = "Вы успешно подписались на автора: {$authorName}";
        return self::send($phone, $text);
    }
}

