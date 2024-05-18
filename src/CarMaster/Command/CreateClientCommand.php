<?php

namespace App\CarMaster\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\CarMaster\Entity\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'carmaster:create-client')]
class CreateClientCommand extends Command
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
            ->addArgument('name', InputArgument::OPTIONAL, 'Client name')
            ->addArgument('email', InputArgument::OPTIONAL, 'Client email')
            ->addArgument('phone', InputArgument::OPTIONAL, 'Client phone');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name') ?: 'defaultName';
        $email = $input->getArgument('email') ?: 'default@example.com';
        $phone = $input->getArgument('phone') ?: '000-000-0000';

        $client = new Client($name, $email, $phone);

        $this->entityManager->persist($client);

        $io->success("Created client with name: $name, email: $email, phone: $phone");

        return Command::SUCCESS;
    }
}
