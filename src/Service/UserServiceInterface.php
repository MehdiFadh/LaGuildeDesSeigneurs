<?php

// src/Service/UserServiceInterface.php
// Interface définissant le contrat du service utilisateur, incluant l'émission de tokens JWT, la recherche par email et l'analyse (parsing) de tokens.

namespace App\Service;

use App\Entity\User;

interface UserServiceInterface
{
    // Gets the token
    public function getToken(User $user);

    // Finds one by email
    public function findOneByEmail(string $token);

    // Parses the token
    public function parseToken(string $token);
}
