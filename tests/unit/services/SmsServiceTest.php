<?php

namespace tests\unit\services;

use app\services\SmsService;
use Yii;

class SmsServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    public $tester;

    protected function _before()
    {
        // Убедиться, что apiKey установлен в 'emulator'
        Yii::$app->params['sms']['apiKey'] = 'emulator';
    }

    public function testSendWithEmulator()
    {
        // Отправить SMS в режиме эмулятора
        $result = SmsService::send('+79001234567', 'Тестовое сообщение');
        
        // Должно вернуть true
        verify($result)->true();
        
        // Проверить, что сообщение залогировано
        // В реальности можно проверить логи, но для простоты проверяем только возврат
    }

    public function testSendSubscriptionConfirmation()
    {
        $phone = '+79001234567';
        $authorName = 'Лев Толстой';
        
        $result = SmsService::sendSubscriptionConfirmation($phone, $authorName);
        
        // Должно вернуть true
        verify($result)->true();
        
        // Проверить, что метод send() был вызван с правильным текстом
        // Текст должен содержать имя автора
        // В реальности можно проверить логи, но для простоты проверяем только возврат
    }

    public function testSendWithDifferentPhones()
    {
        // Проверить отправку на разные телефоны
        $phone1 = '+79001234567';
        $phone2 = '+79009876543';
        
        $result1 = SmsService::send($phone1, 'Сообщение 1');
        $result2 = SmsService::send($phone2, 'Сообщение 2');
        
        verify($result1)->true();
        verify($result2)->true();
    }

    public function testSendWithEmptyText()
    {
        // Проверить отправку с пустым текстом (должно работать)
        $result = SmsService::send('+79001234567', '');
        
        verify($result)->true();
    }

    public function testSendSubscriptionConfirmationWithSpecialCharacters()
    {
        // Проверить отправку с особыми символами в имени автора
        $phone = '+79001234567';
        $authorName = 'Автор "Особый" & Тест';
        
        $result = SmsService::sendSubscriptionConfirmation($phone, $authorName);
        
        verify($result)->true();
    }
}

