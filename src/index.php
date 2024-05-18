<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\CarMaster\Entity\Client;
use App\CarMaster\Entity\Car;
use App\CarMaster\Entity\Service;
use App\CarMaster\Entity\Part;
use App\CarMaster\Entity\Order;
use App\CarMaster\MyEntityManager;
use Faker\Factory;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

$entityManagerFactory = new MyEntityManager();
$entityManager = $entityManagerFactory->createORMEntityManager();

// Symfony Console setup
$input = new ArgvInput();
$output = new ConsoleOutput();
$io = new SymfonyStyle($input, $output);

// Faker setup
$faker = Factory::create();

// Create and persist Clients
$clients = [];
for ($i = 0; $i < 10; $i++) {
    $client = new Client($faker->name(), $faker->email(), $faker->phoneNumber());
    $entityManager->persist($client);
    $clients[] = $client;
}
$entityManager->flush();

// Create and persist Cars
$cars = [];
foreach ($clients as $client) {
    for ($i = 0; $i < rand(1, 3); $i++) {
        $car = new Car();
        $car->setType($faker->randomElement(['Sedan', 'SUV', 'Truck', 'Coupe']));
        $car->setBrand($faker->company());
        $car->setModel($faker->word());
        $car->setYear($faker->numberBetween(1990, 2024));  // Year in the range 1990 to 2024
        $car->setNumber($faker->regexify('[A-Z]{3}[0-9]{3}'));
        $car->setClient($client);
        $entityManager->persist($car);
        $cars[] = $car;
    }
}
$entityManager->flush();

// Create and persist Services
$services = [];
for ($i = 0; $i < 10; $i++) {
    $service = new Service();
    $service->setName($faker->word());
    $service->setCost($faker->randomFloat(2, 50, 500));
    $service->setDuration($faker->numberBetween(30, 180));
    $entityManager->persist($service);
    $services[] = $service;
}
$entityManager->flush();

// Create and persist Parts
$parts = [];
for ($i = 0; $i < 10; $i++) {
    $part = new Part(
        $faker->word(),
        $faker->randomFloat(2, 10, 200),
        $faker->numberBetween(30, 180),
$faker->randomFloat(2, 15, 300));
    $entityManager->persist($part);
    $parts[] = $part;
}
$entityManager->flush();

// Create and persist Orders
$orders = [];
foreach ($clients as $client) {
    for ($i = 0; $i < rand(1, 3); $i++) {
        $order = new Order();
        $order->setClient($client);
        $order->setCar($faker->randomElement($cars));
        $order->setTotalCost(0);

        foreach ($faker->randomElements($services, rand(1, 3)) as $service) {
            $order->addService($service);
            $order->setTotalCost($order->getTotalCost() + $service->getCost());
        }

        $entityManager->persist($order);
        $orders[] = $order;
    }
}
$entityManager->flush();

// Output created data
$io->title('Clients');
$io->table(['ID', 'Name', 'Email', 'Phone'], array_map(function (Client $client) {
    return [$client->getId(), $client->getName(), $client->getEmail(), $client->getPhone()];
}, $clients));

$io->title('Cars');
$io->table(['ID', 'Type', 'Brand', 'Model', 'Year', 'Number', 'Client'], array_map(function (Car $car) {
    return [$car->getId(), $car->getType(), $car->getBrand(), $car->getModel(), $car->getYear(), $car->getNumber(), $car->getClient()->getName()];
}, $cars));

$io->title('Services');
$io->table(['ID', 'Name', 'Cost', 'Duration'], array_map(function (Service $service) {
    return [$service->getId(), $service->getName(), $service->getCost(), $service->getDuration()];
}, $services));

$io->title('Parts');
$io->table(['ID', 'Name', 'Cost', 'Quantity', 'Selling Price'], array_map(function (Part $part) {
    return [$part->getId(), $part->getName(), $part->getCost(), $part->getQuantity(), $part->getSellingPrice()];
}, $parts));

$io->title('Orders');
$io->table(['ID', 'Client', 'Car', 'Total Cost', 'Creation Date'], array_map(function (Order $order) {
    return [$order->getId(), $order->getClient()->getName(), $order->getCar()->getBrand() . ' ' . $order->getCar()->getModel(), $order->getTotalCost(), $order->getCreationDate()->format('Y-m-d H:i:s')];
}, $orders));

// Export Cars to CSV
$exportCommand = new \App\CarMaster\Command\ExportAllCarsCommand($entityManager);
$exportCommand->run($input, $output);
