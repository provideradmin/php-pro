<?php
declare(strict_types=1);

namespace CarMaster;

require dirname(__DIR__) . '/vendor/autoload.php';

use CarMaster\Exceptions\CarException;
use CarMaster\Exceptions\InventoryException;
use Faker\Factory;

$faker = Factory::create();
$client1 = new Client($faker->name, $faker->email, $faker->phoneNumber);

echo "Данные клиента:\n";
echo "Имя: {$client1->getName()}\n";
echo "Email: {$client1->getEmail()}\n";
echo "Телефон: {$client1->getPhone()}\n";


try {
    $car1 = new Car('Sedan', 'Toyota', 'Camry', 2018, 'ABC123', $client1);
    $car1->validate();
    echo "Машина {$car1->getNumber()} добавлена.\n";
} catch (CarException $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}

try {
    $car2 = new Car('SUV', 'Ford', 'Explorer', 2019, 'XYZ456', $client1);
    $car2->validate();
    echo "Машина {$car2->getNumber()} добавлена\n";
} catch (CarException $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}

try {
    $car3 = new Car('SUV', 'Ford', 'Explorer', 2019, 'XYZ456', $client1);
    $car3->validate();  //не создастся
    echo "Машина {$car3->getNumber()} добавлена.\n";
} catch (CarException $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
$client1->addCar($car1);
$client1->addCar($car2);

// Создаем услугу, запчасти и материалы
$service = new Service('Замена масла', 100.00, 1);

$parts = [
    new Part('колодки', 500.00, 2, 700.00),
    new Part('фильтр', 100.00, 1, 150.00)
];

$materials = [
    new Material('Масло', 20.00, 1),
    new Material('Смазка', 15.00, 1)
];

// Создаем заказ
$order = new Order('121212', $service, $parts, $materials, $client1, $car1);

// Выводим информацию о заказе и его стоимости
echo "Номер заказа: {$order->getOrderNumber()}\n";
echo "Дата: {$order->getCreationDate()}\n";
echo "Клиент: {$order->getClient()->getName()}\n";
echo "Машина: {$order->getCar()->getBrand()} {$order->getCar()->getModel()} ({$order->getCar()->getYear()})\n";
echo "Услуга: {$order->getService()->getName()}\n";
echo "Запчасти:\n";
foreach ($order->getParts() as $part) {
    echo "- {$part->getName()}: {$part->getQuantity()} x {$part->getCost()} грн\n";
}
echo "Материалы:\n";
foreach ($order->getMaterials() as $material) {
    echo "- {$material->getName()}: {$material->getQuantity()} x {$material->getCost()} грн\n";
}
echo "Всего: {$order->getTotalCost()} грн\n";

// Принимаем оплату за заказ
$order->markAsPaid(date('Y-m-d H:i:s'));
echo "Статус оплаты: " . ($order->isPaid() ? "оплачено" : "неоплачено") . "\n";
echo "Дата платежа: {$order->getPaymentDate()}\n";

// Продаем запчасть без выполнения заказа
$partToSell = new Part('Лампочки', 5.00, 1, 8.00);
$partToSell->sell(1);
echo "Продано: {$partToSell->getName()} за {$partToSell->getSellingPrice()} грн\n";

// Пополняем запчасти и материалы на складе
$partsToReplenish = [
    new Part('Щетки', 20.00, 5, 25.00),
    new Part('Фильтр', 15.00, 3, 20.00)
];
$materialsToReplenish = [
    new Material('Тормозная жидкость', 10.00, 2),
    new Material('Антифриз', 8.00, 2)
];
foreach ($partsToReplenish as $part) {
    $part->addToInventory($part->getQuantity());
    echo "Запчасти: {$part->getName()}, кол-во: {$part->getQuantity()}\n";
}
foreach ($materialsToReplenish as $material) {
    $material->addToInventory($material->getQuantity());
    echo "Расходники: {$material->getName()}, кол-во: {$material->getQuantity()}\n";
}

try {
    // Создание товара "Шины" с начальным количеством 10 и стоимостью 500.00 за штуку
    $tires = new Part('Шины', 500.00, 10, 1000);

    // Вывод информации о товаре
    echo "Товар: {$tires->getName()}, Количество: {$tires->getQuantity()}, Стоимость: {$tires->getCost()} руб.\n";

    // Попытка списания больше чем есть
    $quantityToRemove = 15;
    $tires->removeFromInventory($quantityToRemove);

    // Если удаление прошло успешно, выведем информацию о товаре после списания
    echo "Товар: {$tires->getName()}, Количество: {$tires->getQuantity()}.\n";
} catch (InventoryException $e) {
    // Вычисляем недостающее количество как разницу между запрошенным списанием и остатком на складе
    $missingQuantity = $quantityToRemove - $tires->getQuantity();

    // Ловим исключение и выводим сообщение об ошибке, указывая количество недостающих товаров
    echo "Ошибка: {$e->getMessage()}. Не хватает: $missingQuantity шт.\n";
}
