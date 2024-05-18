<?php

namespace App\CarMaster\Command;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory as FakerFactory;
use App\CarMaster\Entity\Material;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'carmaster:create-material')]
class CreateMaterialCommand extends Command
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
            ->addArgument('name', InputArgument::OPTIONAL, 'Material name')
            ->addArgument('cost', InputArgument::OPTIONAL, 'Material cost')
            ->addArgument('quantity', InputArgument::OPTIONAL, 'Material quantity');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $faker = FakerFactory::create();

        $name = $input->getArgument('name') ?: $faker->word();
        $cost = (float)($input->getArgument('cost') ?: $faker->randomFloat(2, 10, 1000));
        $quantity = (int)($input->getArgument('quantity') ?: $faker->numberBetween(1, 100));

        $material = new Material();
        $material->setName($name);
        $material->setCost($cost);
        $material->setQuantity($quantity);

        $this->entityManager->persist($material);

        $io->success("Created material with name: $name, cost: $cost, quantity: $quantity");

        return Command::SUCCESS;
    }
}