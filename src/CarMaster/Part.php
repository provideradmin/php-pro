<?php

declare(strict_types=1);

namespace CarMaster;

use CarMaster\Product;

class Part extends Product
{
    private float $sellingPrice;

    public function __construct(string $name, float $cost, int $quantity, float $sellingPrice)
    {
        parent::__construct($name, $cost, $quantity);
        $this->sellingPrice = $sellingPrice;
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
}
