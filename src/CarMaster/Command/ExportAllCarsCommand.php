<?php

declare(strict_types=1);

namespace App\CarMaster\Command;

use App\CarMaster\Entity\Car;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'carmaster:export-all-cars', description: 'Export all cars to CSV')]
class ExportAllCarsCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        // No need to add exportDirectory argument
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Set export directory to /files
        $exportDirectory = __DIR__ . '/../../../../files';
        if (!is_dir($exportDirectory)) {
            mkdir($exportDirectory, 0777, true);
        }
        $exportFilename = $exportDirectory . '/cars.csv';

        $query = $this->entityManager
            ->getRepository(Car::class)
            ->createQueryBuilder('c')
            ->leftJoin('c.client', 'cl')
            ->addSelect('cl')
            ->getQuery();

        $file = fopen($exportFilename, 'w');

        // Add CSV header
        fputcsv($file, ['Car ID', 'Type', 'Brand', 'Model', 'Year', 'Number', 'Client ID', 'Client Name', 'Client Email', 'Client Phone']);

        $ormTimeStart = microtime(true);
        foreach ($query->toIterable() as $car) {
            /** @var Car $car */
            $client = $car->getClient();
            fputcsv($file, [
                $car->getId(),
                $car->getType(),
                $car->getBrand(),
                $car->getModel(),
                $car->getYear(),
                $car->getNumber(),
                $client->getId(),
                $client->getName(),
                $client->getEmail(),
                $client->getPhone()
            ]);
            $this->entityManager->detach($car);
        }
        $ormTimeEnd = microtime(true);

        fclose($file);

        $io->success(sprintf(
            "Exported all cars to %s\nMemory usage: %0.2f Mb\nTime: %d sec",
            $exportFilename,
            memory_get_usage(true) / 1000000,
            $ormTimeEnd - $ormTimeStart
        ));

        return Command::SUCCESS;
    }
}
