<?php

declare(strict_types=1);

namespace CarMaster;

use PDO;

class PartRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(Part $part): int
    {
        $query = "INSERT INTO parts (name, cost, quantity, selling_price) VALUES (?, ?, ?, ?)";
        $statement = $this->pdo->prepare($query);
        $success = $statement->execute([$part->getName(), $part->getCost(), $part->getQuantity(), $part->getSellingPrice()]);

        if ($success) {
            return (int)$this->pdo->lastInsertId();
        } else {
            throw new \RuntimeException("Почему-то не вставилось.");
        }
    }

    public function update(Part $part): bool
    {
        $query = "UPDATE parts SET name = ?, cost = ?, quantity = ?, selling_price = ? WHERE id = ?";
        $statement = $this->pdo->prepare($query);
        return $statement->execute([$part->getName(), $part->getCost(), $part->getQuantity(), $part->getSellingPrice(), $part->getId()]);
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM parts WHERE id = ?";
        $statement = $this->pdo->prepare($query);
        return $statement->execute([$id]);
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM parts";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $partsData = $statement->fetchAll(PDO::FETCH_ASSOC);

        $parts = [];
        foreach ($partsData as $partData) {
            $parts[] = new Part(
                $partData['name'],
                (float)$partData['cost'],
                (int)$partData['quantity'],
                (float)$partData['selling_price'],
                (int)$partData['id']
            );
        }

        return $parts;
    }
}
