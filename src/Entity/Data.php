<?php

namespace App\Entity;

class Data
{
    protected int $vehicleId;
    protected \DateTime $date;
    protected int $odo;
    protected float $fuel;
    protected float $price;
    protected float $volumePrice;
    protected ?string $notes;
    protected float $average;
    protected ?string $city;

    public function getVehicleId(): int
    {
        return $this->vehicleId;
    }

    public function setVehicleId(int $vehicleId): void
    {
        $this->vehicleId = $vehicleId;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getOdo(): int
    {
        return $this->odo;
    }

    public function setOdo(int $odo): void
    {
        $this->odo = $odo;
    }

    public function getFuel(): float
    {
        return $this->fuel;
    }

    public function setFuel(float $fuel): void
    {
        $this->fuel = $fuel;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getVolumePrice(): float
    {
        return $this->volumePrice;
    }

    public function setVolumePrice(float $volumePrice): void
    {
        $this->volumePrice = $volumePrice;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getAverage(): float
    {
        return $this->average;
    }

    public function setAverage(float $average): void
    {
        $this->average = $average;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }
}
