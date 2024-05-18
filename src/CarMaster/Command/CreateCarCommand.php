<?php

namespace App\CarMaster\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\CarMaster\Entity\Car;
use App\CarMaster\Entity\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'carmaster:create-car')]
class CreateCarCommand extends Command
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
            ->addArgument('type', InputArgument::OPTIONAL, 'Car type')
            ->addArgument('brand', InputArgument::OPTIONAL, 'Car brand')
            ->addArgument('model', InputArgument::OPTIONAL, 'Car model')
            ->addArgument('year', InputArgument::OPTIONAL, 'Car year')
            ->addArgument('number', InputArgument::OPTIONAL, 'Car number')
            ->addArgument('client_id', InputArgument::OPTIONAL, 'Client ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $type = $input->getArgument('type') ?: 'defaultType';
        $brand = $input->getArgument('brand') ?: 'defaultBrand';
        $model = $input->getArgument('model') ?: 'defaultModel';
        $year = (int)($input->getArgument('year') ?: date('Y'));
        $number = $input->getArgument('number') ?: 'ABC123';
        $clientId = $input->getArgument('client_id');

        $client = $this->entityManager->find(Client::class, $clientId);
        if (!$client) {
            $io->error("Client with ID $clientId not found.");
            return Command::FAILURE;
        }

        $car = new Car();
        $car->setType($type);
        $car->setBrand($brand);
        $car->setModel($model);
        $car->setYear($year);
        $car->setNumber($number);
        $car->setClient($client);

        $this->entityManager->persist($car);

        $io->success("Created car with type: $type, brand: $brand, model: $model, year: $year, number: $number");

        return Command::SUCCESS;
    }
}
