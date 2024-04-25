<?php
declare(strict_types=1);

namespace CarMaster;

use CarMaster\Exceptions\CarException;

class Car
{
    private static array $existingNumbers = [];

    private string $type;
    private string $brand;
    private string $model;
    private int $year;
    private string $number;
    private Client $client; // Ссылка на клиента

    public function __construct(string $type, string $brand, string $model, int $year, string $number, Client $client)
    {
        $this->type = $type;
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
        $this->number = $number;
        $this->client = $client;
    }

    public function validate(): void
    {
        if (in_array($this->number, self::$existingNumbers)) {
            throw new CarException("Авто с номером $this->number уже есть в базе.", $this->number);
        } else {
            self::$existingNumbers[] = $this->number; //пишем в массив номеров
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}