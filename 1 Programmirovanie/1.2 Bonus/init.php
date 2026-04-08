<?php

use \Bitrix\Main\Loader;
use \Bitrix\Sale\Order;
use \Bitrix\Sale\Internals\PaySystemActionTable;
use \Bitrix\Main\EventManager;


if (Loader::includeModule('sale')) {
    
    // Регистрируем обработчик на событие изменения статуса заказа
    EventManager::getInstance()->addEventHandler(
        'sale',
        'OnSaleStatusOrder',
        ['BonusHandler', 'onSaleStatusOrderHandler']
    );
}

class BonusHandler
{
    /**
     * Обработчик смены статуса заказа
     * @param int $orderId - ID заказа
     * @param string $status - Новый статус
     */

    public static function onSaleStatusOrderHandler($orderId, $status)
    {
	self::writeLog("Событие вызвано. Заказ ID: {$orderId}, Статус: {$status}");
	
        $targetStatus = 'F'; 
        
        if ($status !== $targetStatus) {
	    self::writeLog("Статус {$status} не равен целевому {$targetStatus}. Выход.");
            return;
        }

	self::writeLog("Статус совпал. Продолжаем обработку");

        $order = Order::load($orderId);
        if (!$order) {
	    self::writeLog("Ошибка: Не удалось загрузить заказ {$orderId}");
            return;
        }

        if ($order->getField('COMMENTS') === 'Бонусы зачислены') {
            return;
        }

        $basket = $order->getBasket();
        $itemsTotalPrice = 0;
        foreach ($basket as $basketItem) {
            $itemsTotalPrice += $basketItem->getFinalPrice() * $basketItem->getQuantity();
        }

        if ($itemsTotalPrice <= 5000) {
            return;
        }

        $bonusAmount = $itemsTotalPrice * 0.05;
        $userId = $order->getUserId();
        $currency = $order->getCurrency();

        $account = \CSaleUserAccount::GetByUserID($userId, $currency);
        
        if (!$account) {
            $accountId = \CSaleUserAccount::Add([
                'USER_ID' => $userId,
                'CURRENCY' => $currency,
                'CURRENT_BUDGET' => 0
            ]);
        }

        $result = \CSaleUserAccount::UpdateAccount(
            $userId,                                 // ID пользователя
            $bonusAmount,                            // Сумма (положительное число)
            $currency,                               // Валюта
            "Начисление 5% бонусов за заказ №{$orderId}", // Комментарий
            $orderId                                 // ID заказа для связи
        );

        if ($result) {
            $order->setField('COMMENTS', 'Бонусы зачислены');
            $order->save();
        }

	self::writeLog("Обработка заказа {$orderId} завершена. Сумма товаров: {$itemsTotalPrice}");
    }

    //Запись логов
    private static function writeLog($message)
    {
        $logFile = $_SERVER['DOCUMENT_ROOT'] . '/upload/logs/bonus_debug.log';
        $dir = dirname($logFile);
        
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $date = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[{$date}] {$message}\n", FILE_APPEND);
    }
}
?>
