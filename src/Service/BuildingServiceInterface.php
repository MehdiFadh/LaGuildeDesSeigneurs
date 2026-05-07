<?php

namespace App\Service;

use App\Entity\Building;

interface BuildingServiceInterface
{
    public function findAll(): array;

    public function delete(Building $building): void;

    public function create(string $data): Building;

    public function isEntityFilled(Building $building);

    public function submit(Building $building, $formName, $data);

    public function update(Building $building, string $data): void;

    // Serializes the object(s)
    public function serializeJson($object);

    // Finds all the buildings paginated
    public function findAllPaginated($query);

    // Defines the links for HATEOAS
    public function setLinks($object);

    // Gets random images
    public function getImages(int $number);
}
