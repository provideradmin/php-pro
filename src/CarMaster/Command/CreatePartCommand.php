<?php

namespace App\CarMaster\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\CarMaster\Entity\Part;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'carmaster:create-part')]
class CreatePartCommand extends Command
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
            ->addArgument('name', InputArgument::REQUIRED, 'Part name')
            ->addArgument('cost', InputArgument::REQUIRED, 'Part cost')
            ->addArgument('quantity', InputArgument::REQUIRED, 'Part quantity')
            ->addArgument('selling_price', InputArgument::REQUIRED, 'Part selling price');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');
        $cost = (float)$input->getArgument('cost');
        $quantity = (int)$input->getArgument('quantity');
        $sellingPrice = (float)$input->getArgument('selling_price');

        $part = new Part($name, $cost, $quantity, $sellingPrice);

        $this->entityManager->persist($part);

        $io->success("Created part with name: $name, cost: $cost, quantity: $quantity, selling price: $sellingPrice");

        return Command::SUCCESS;
    }
}
