<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Character;
use App\Service\CharacterServiceInterface;

final class CharacterController extends AbstractController
{
    #[Route('/characters/', name: 'app_character_display', methods: ['GET'])]
    public function display(): JsonResponse
    {
        $character = new Character();
        return new JsonResponse($character->toArray());
    }

    #[Route('/characters/', name: 'app_character_create', methods: ['POST'])]
    // "methods: ['POST']" permet d'interdire GET pour la création
    public function create(): JsonResponse
    {
        $character = $this->characterService->create();
        return new JsonResponse($character->toArray(), JsonResponse::HTTP_CREATED);
    }

    public function __construct(
        private CharacterServiceInterface $characterService
    ) {
    }
}
