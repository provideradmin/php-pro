<?php
declare(strict_types=1);

namespace App\CarMaster\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{Column, Entity, GeneratedValue, Id, JoinTable, ManyToMany, Table};

#[Entity]
#[Table(name: 'service')]
class Service
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[Column(length: 255)]
    private string $name;

    #[Column(type: Types::FLOAT)]
    private float $cost;

    #[Column(type: Types::INTEGER)]
    private int $duration;

    #[ManyToMany(targetEntity: Order::class, mappedBy: 'services')]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    // Getters and setters...

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function setCost(float $cost): void
    {
        $this->cost = $cost;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): void
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->addService($this);
        }
    }

    public function removeOrder(Order $order): void
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            $order->removeService($this);
        }
    }
}
