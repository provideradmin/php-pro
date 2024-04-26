<?php

declare(strict_types=1);

namespace CarMaster;

use CarMaster\Exceptions\InventoryException;

abstract class Product
{
    protected string $name;
    protected float $cost;
    protected int $quantity;

    public function __construct(string $name, float $cost, int $quantity)
    {
        $this->name = $name;
        $this->cost = $cost;
        $this->quantity = $quantity;
    }

    // Геттеры и сеттеры

    public function getName(): string
    {
        return $this->name;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setCost(float $cost): void
    {
        $this->cost = $cost;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    // Методы бизнес-логики

    public function addToInventory(int $quantity): void
    {
        $this->quantity += $quantity;
    }

    public function removeFromInventory(int $quantity): void
    {
        if ($quantity <= $this->quantity) {
            $this->quantity -= $quantity;
        } else {
            // Обработка ошибки: попытка списания большего количества, чем имеется на складе
            throw new InventoryException('Недостаточное количество товара на складе.');
        }
    }

// общий функционал по сбору данных про продукт выношу в родительский класс
    public function getProductData(): array
    {
        return [
            'name' => $this->getName(),
            'quantity' => $this->getQuantity(),
            'cost' => $this->getCost()
        ];
    }
}

