<?php
declare(strict_types=1);

namespace App\CarMaster\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{Column, Entity, GeneratedValue, Id, JoinColumn, ManyToOne, ManyToMany, Table};

#[Entity]
#[Table(name: '`order`')]
class Order
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[Column(name: 'creation_date', type: Types::DATETIME_MUTABLE)]
    private \DateTime $creationDate;

    #[Column(name: 'total_cost', type: Types::FLOAT)]
    private float $totalCost;

    #[Column(name: 'payment_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $paymentDate = null;

    #[ManyToOne(targetEntity: Client::class)]
    #[JoinColumn(name: 'client_id', nullable: false)]
    private Client $client;

    #[ManyToOne(targetEntity: Car::class)]
    #[JoinColumn(name: 'car_id', nullable: false)]
    private Car $car;

    #[ManyToMany(targetEntity: Service::class, inversedBy: 'orders')]
    private Collection $services;

    public function __construct()
    {
        $this->creationDate = new \DateTime();
        $this->services = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }

    // Setter for creationDate is removed to prevent modifications

    public function getTotalCost(): float
    {
        return $this->totalCost;
    }

    public function setTotalCost(float $totalCost): void
    {
        $this->totalCost = $totalCost;
    }

    public function getPaymentDate(): ?\DateTime
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTime $paymentDate): void
    {
        $this->paymentDate = $paymentDate;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function getCar(): Car
    {
        return $this->car;
    }

    public function setCar(Car $car): void
    {
        $this->car = $car;
    }

    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): void
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
        }
    }

    public function removeService(Service $service): void
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
        }
    }
}
