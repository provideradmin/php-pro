<?php
declare(strict_types=1);
namespace CarMaster;
class Service {
    private string $name;
    private float $cost;
    private int $duration;

    public function __construct(string $name, float $cost, int $duration) {
        $this->name = $name;
        $this->cost = $cost;
        $this->duration = $duration;
    }

    // Геттеры

    public function getName(): string {
        return $this->name;
    }

    public function getCost(): float {
        return $this->cost;
    }

    public function getDuration(): int {
        return $this->duration;
    }

    // Сеттеры

    public function setCost(float $cost): void {
        $this->cost = $cost;
    }

    public function setDuration(int $duration): void {
        $this->duration = $duration;
    }
}

