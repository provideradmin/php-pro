<?php
declare(strict_types=1);

namespace CarMaster;

class Material extends Product
{
    public function updateCost(float $newCost): void
    {
        $this->setCost($newCost);
    }

    public function getName(): string
    {
        return "Расходный материал " . parent::getName();
    }
}

