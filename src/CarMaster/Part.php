<?php

declare(strict_types=1);

namespace CarMaster;

class Part extends Product
{
    private float $sellingPrice;
    private ?int $id; // добавлено свойство для хранения ID

    public function __construct(string $name, float $cost, int $quantity, float $sellingPrice, ?int $id = null)
    {
        parent::__construct($name, $cost, $quantity);
        $this->sellingPrice = $sellingPrice;
        $this->id = $id;
    }

    public function getSellingPrice(): float
    {
        return $this->sellingPrice;
    }

    public function setSellingPrice(float $sellingPrice): void
    {
        $this->sellingPrice = $sellingPrice;
    }

    public function sell(int $quantity): void
    {
        $this->removeFromInventory($quantity);
    }

    public function getType(): string
    {
        return 'parts';
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
