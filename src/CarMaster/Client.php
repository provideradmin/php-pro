<?php
declare(strict_types=1);

namespace CarMaster;

use CarMaster\Car;

class Client
{
    private string $name;
    private string $email;
    private string $phone;
    private string $registrationDate;
    private array $cars = [];

    public function __construct(string $name, string $email, string $phone)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->registrationDate = date('Y-m-d H:i:s'); // Текущая дата и время
    }

    // Геттеры

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getRegistrationDate(): string
    {
        return $this->registrationDate;
    }

    public function getCars(): array
    {
        return $this->cars;
    }

    // Сеттеры

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    // Добавление машины клиенту
    public function addCar(Car $car): void
    {
        $this->cars[] = $car;
    }
}
