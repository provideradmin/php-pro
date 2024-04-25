<?php
declare(strict_types=1);
namespace CarMaster;

use CarMaster\Client;
use CarMaster\Car;

class Order
{
    private string $orderNumber;
    private string $creationDate;
    private Service $service;
    private array $parts;
    private array $materials;
    private Client $client;
    private Car $car;
    private bool $paid;
    private ?string $paymentDate;

    public function __construct(string $orderNumber, Service $service, array $parts, array $materials, Client $client, Car $car)
    {
        $this->orderNumber = $orderNumber;
        $this->creationDate = date('Y-m-d H:i:s'); // Текущая дата и время
        $this->service = $service;
        $this->parts = $parts;
        $this->materials = $materials;
        $this->client = $client;
        $this->car = $car;
        $this->paid = false; // По умолчанию заказ не оплачен
        $this->paymentDate = null; // По умолчанию дата оплаты не установлена
    }

    // Геттеры

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getCreationDate(): string
    {
        return $this->creationDate;
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function getParts(): array
    {
        return $this->parts;
    }

    public function getMaterials(): array
    {
        return $this->materials;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getCar(): Car
    {
        return $this->car;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function getPaymentDate(): ?string
    {
        return $this->paymentDate;
    }

    // Метод для подсчета общей стоимости заказа
    public function getTotalCost(): float
    {
        $totalCost = $this->service->getCost();
        foreach ($this->parts as $part) {
            $totalCost += $part->getCost();
        }
        foreach ($this->materials as $material) {
            $totalCost += $material->getCost();
        }
        return $totalCost;
    }

    // Методы для изменения статуса оплаты и установки даты оплаты
    public function markAsPaid(string $paymentDate): void
    {
        $this->paid = true;
        $this->paymentDate = $paymentDate;
    }

    public function markAsUnpaid(): void
    {
        $this->paid = false;
        $this->paymentDate = null;
    }
}

?>
