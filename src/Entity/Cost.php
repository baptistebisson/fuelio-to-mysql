<?php

namespace App\Entity;

class Cost
{
    protected \DateTime $date;
    protected string $title;
    protected ?float $odo;
    protected ?string $notes;
    protected float $cost;
    protected int $costCategory;

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getOdo(): ?float
    {
        return $this->odo;
    }

    public function setOdo(?float $odo): void
    {
        $this->odo = $odo;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function setCost(float $cost): void
    {
        $this->cost = $cost;
    }

    public function getCostCategory(): int
    {
        return $this->costCategory;
    }

    public function setCostCategory(int $costCategory): void
    {
        $this->costCategory = $costCategory;
    }
}
