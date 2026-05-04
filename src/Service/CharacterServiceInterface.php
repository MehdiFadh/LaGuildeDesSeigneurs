<?php
namespace App\Service;
use App\Entity\Character;
interface CharacterServiceInterface
{
// Creates the character
public function create(): Character;

// Finds all the characters
public function findAll(): array;

public function update(Character $character): void;

public function delete(Character $character): void;

}