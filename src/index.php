<?php

declare(strict_types=1);

namespace CarMaster;

require dirname(__DIR__) . '/vendor/autoload.php';

use CarMaster\Exceptions\CarException;
use CarMaster\Exceptions\InventoryException;
use Faker\Factory;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDO;

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

// Инициализация Symfony Console
$input = new ArgvInput();
$output = new ConsoleOutput();
$style = new SymfonyStyle($input, $output);

// добавим клиентов для таблички
$client2 = new Client($faker->name, $faker->email, $faker->phoneNumber);
$client3 = new Client($faker->name, $faker->email, $faker->phoneNumber);


// Вывод информации о клиентах
$style->title('Данные клиентов:');
$clientsData = [
    [$client1->getName(), $client1->getEmail(), $client1->getPhone()],
    [$client2->getName(), $client2->getEmail(), $client2->getPhone()],
    [$client3->getName(), $client3->getEmail(), $client3->getPhone()]
];
$style->table(['Имя', 'Email', 'Телефон'], $clientsData, 'compact');


// Вывод информации о заказе
$style->title('Данные заказа:');
$style->writeln("Номер заказа: <info>{$order->getOrderNumber()}</info>");
$style->writeln("Дата: <info>{$order->getCreationDate()}</info>");
$style->writeln("Клиент: <info>{$order->getClient()->getName()}</info>");
$style->writeln(
    "Машина: <info>{$order->getCar()->getBrand()} {$order->getCar()->getModel()} ({$order->getCar()->getYear()})</info>"
);
$style->writeln("Услуга: <info>{$order->getService()->getName()}</info>");

// Вывод информации о запчастях
$partsData = [];
foreach ($order->getParts() as $part) {
    $partsData[] = [$part->getName(), "{$part->getQuantity()} x {$part->getCost()} грн"];
}
$style->table(['Запчасти', 'Стоимость'], $partsData);

// Вывод информации о материалах
$materialsData = [];
foreach ($order->getMaterials() as $material) {
    $materialsData[] = [$material->getName(), "{$material->getQuantity()} x {$material->getCost()} грн"];
}
$style->table(['Материалы', 'Стоимость'], $materialsData);

// Вывод общей стоимости заказа
$style->success("Всего: {$order->getTotalCost()} грн");


// Создаем экземпляр Filesystem
$filesystem = new Filesystem();

// Путь к папке для файла заказа
$directoryPath = dirname(__DIR__) . '/files/';
// Проверяем существование папки, и если она уже есть, удаляем ее со всем содержимым
if ($filesystem->exists($directoryPath)) {
    try {
        $filesystem->remove($directoryPath);
    } catch (IOExceptionInterface $exception) {
        echo "Ошибка при удалении папки: " . $exception->getMessage() . "\n";
    }
}

// Создаем папку для файла заказа
try {
    $filesystem->mkdir($directoryPath);
    echo "Папка для заказа успешно создана.\n";
} catch (IOExceptionInterface $exception) {
    echo "Ошибка при создании папки для заказа: " . $exception->getMessage() . "\n";
}

// Путь к файлу заказа
$filePath = $directoryPath . '/order.json';

// Создаем данные заказа для записи в файл
$orderData = [
    'order_number' => $order->getOrderNumber(),
    'creation_date' => $order->getCreationDate(),
    'client_name' => $order->getClient()->getName(),
    'car_info' => $order->getCar()->getBrand() . ' ' . $order->getCar()->getModel() . ' (' . $order->getCar()->getYear() . ')',
    'service_name' => $order->getService()->getName(),
    'parts' => [],
    'materials' => [],
    'total_cost' => $order->getTotalCost()
];

// Добавляем информацию о материалах
$order->addProductsToOrderData($order->getParts());

//// Добавляем информацию о запчастях
$order->addProductsToOrderData($order->getMaterials());


// Преобразуем данные в формат JSON
$orderJson = json_encode($orderData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// Создаем логгер Monolog
$log = new Logger('orders');
$log->pushHandler(new StreamHandler(dirname(__DIR__) . '/log/orders.log', Logger::INFO));

// Записываем данные заказа в файл
try {
    $filesystem->dumpFile($filePath, $orderJson);
    echo "Данные заказа успешно записаны в файл.\n";

    // Записываем информацию о записи в лог
    $log->info('Данные заказа успешно записаны в файл', ['file_path' => $filePath]);
} catch (IOExceptionInterface $exception) {
    echo "Ошибка при записи данных заказа в файл: " . $exception->getMessage() . "\n";

    // Записываем информацию об ошибке в лог
    $log->error('Ошибка при записи данных заказа в файл', ['exception_message' => $exception->getMessage()]);
}

$host = 'db';
$port = 3306;
$dbname = 'carmaster_db';
$username = 'carmaster_user';
$password = 'carmaster123';


$dsn = "mysql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $username, $password);
    echo "Подключились к базе";
} catch (PDOException $e) {
    die("ошибка подключения: " . $e->getMessage());
}

$faker = Factory::create();

$partRepository = new PartRepository($pdo);

// Заполняем таблицу `parts` 10 записями
for ($i = 0; $i < 10; $i++) {
    $name = $faker->word;
    $cost = $faker->randomFloat(2, 10, 1000);
    $quantity = $faker->numberBetween(1, 1000);
    $sellingPrice = $faker->randomFloat(2, $cost, $cost + 500);

    $part = new Part($name, $cost, $quantity, $sellingPrice);

    // Добавляем новую запись в таблицу
    $partRepository->create($part);
}
echo "Таблица parts заполнена 10 записями.\n";

// Выводим все с изменениями
$parts = $partRepository->findAll();
$output = new ConsoleOutput();
$io = new SymfonyStyle($input, $output);
$io->section("All parts:");
$io->table(['ID', 'Name', 'Cost', 'Quantity', 'Selling Price'], array_map(function ($part) {
    return [$part->getId(), $part->getName(), $part->getCost(), $part->getQuantity(), $part->getSellingPrice()];
}, $parts));
// Обновляем первую запчасть
$partToUpdate = $parts[0];
$partToUpdate->setName('Запчасть обновленная');
$partToUpdate->setCost(15.99);
$partRepository->update($partToUpdate);
echo "Обновили запчасть с ID: " . $partToUpdate->getId() . PHP_EOL;

// Получаем последние 5 записей
$partsToDelete = array_slice($parts, -5);
// Удаляем каждую запись
foreach ($partsToDelete as $partToDelete) {
    $partRepository->delete($partToDelete->getId());
}

// Выводим все с изменениями
$output = new ConsoleOutput();
$io = new SymfonyStyle($input, $output);
$io->section("Обновили запчасти:");
$io->table(['ID', 'Name', 'Cost', 'Quantity', 'Selling Price'], array_map(function ($part) {
    return [$part->getId(), $part->getName(), $part->getCost(), $part->getQuantity(), $part->getSellingPrice()];
}, $parts));