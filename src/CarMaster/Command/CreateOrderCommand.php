<?php

namespace App\CarMaster\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\CarMaster\Entity\Order;
use App\CarMaster\Entity\Client;
use App\CarMaster\Entity\Car;
use App\CarMaster\Entity\Service;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'carmaster:create-order')]
class CreateOrderCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('clientId', InputArgument::OPTIONAL, 'Client ID')
            ->addArgument('carId', InputArgument::OPTIONAL, 'Car ID')
            ->addArgument('serviceIds', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Service IDs (space separated)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $clientId = $input->getArgument('clientId');
        $carId = $input->getArgument('carId');
        $serviceIds = $input->getArgument('serviceIds');

        $client = $this->entityManager->getRepository(Client::class)->find($clientId);
        if (!$client) {
            $io->error("Client with ID $clientId not found.");
            return Command::FAILURE;
        }

        $car = $this->entityManager->getRepository(Car::class)->find($carId);
        if (!$car) {
            $io->error("Car with ID $carId not found.");
            return Command::FAILURE;
        }

        $order = new Order();
        $order->setClient($client);
        $order->setCar($car);
        $order->setCreationDate(new \DateTime());
        $order->setTotalCost(0);

        foreach ($serviceIds as $serviceId) {
            $service = $this->entityManager->getRepository(Service::class)->find($serviceId);
            if ($service) {
                $order->addService($service);
                $order->setTotalCost($order->getTotalCost() + $service->getCost());
            } else {
                $io->warning("Service with ID $serviceId not found.");
            }
        }

        $this->entityManager->persist($order);

        $io->success("Created order for client ID: $clientId, car ID: $carId, with services: " . implode(', ', $serviceIds));

        return Command::SUCCESS;
    }
}
