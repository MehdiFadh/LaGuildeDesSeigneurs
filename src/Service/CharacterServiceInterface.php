<?php
namespace App\Service;
use App\Entity\Character;
interface CharacterServiceInterface
{
    // Creates the character
    public function create(string $data): Character;

    // Finds all the characters
    public function findAll(): array;

    public function delete(Character $character): void;

    // Checks if the entity has been well filled
    public function isEntityFilled(Character $character);
    // Submits the data to hydrate the object
    public function submit(Character $character, $formName, $data);

    public function update(Character $character, string $data): void;
    

}