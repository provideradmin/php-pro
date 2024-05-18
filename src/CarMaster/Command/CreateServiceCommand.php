<?php

namespace App\CarMaster\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\CarMaster\Entity\Service;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'carmaster:create-service')]
class CreateServiceCommand extends Command
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
            ->addArgument('name', InputArgument::REQUIRED, 'Service name')
            ->addArgument('cost', InputArgument::REQUIRED, 'Service cost')
            ->addArgument('duration', InputArgument::REQUIRED, 'Service duration in minutes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');
        $cost = (float)$input->getArgument('cost');
        $duration = (int)$input->getArgument('duration');

        $service = new Service($name, $cost, $duration);

        $this->entityManager->persist($service);

        $io->success("Created service with name: $name, cost: $cost, duration: $duration minutes");

        return Command::SUCCESS;
    }
}
