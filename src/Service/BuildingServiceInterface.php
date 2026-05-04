<?php

namespace App\Service;

use App\Entity\Building;

interface BuildingServiceInterface
{
    public function create(): Building;
    public function findAll(): array;
    public function update(Building $building): void;
    public function delete(Building $building): void;
}
