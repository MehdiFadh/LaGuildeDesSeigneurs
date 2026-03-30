<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Character;
use App\Service\CharacterServiceInterface;

final class CharacterController extends AbstractController
{
    #[Route('/characters/{identifier:character}', requirements: ['identifier' => '^([a-z0-9]{40})$'], name: 'app_character_display', methods: ['GET'])]
    public function display(Character $character): JsonResponse
    {
        return new JsonResponse($character->toArray());
    }

    #[Route('/characters/', name: 'app_character_create', methods: ['POST'])]
    // "methods: ['POST']" permet d'interdire GET pour la création
    public function create(): JsonResponse
    {
        $character = $this->characterService->create();
        $response = new JsonResponse($character->toArray(), JsonResponse::HTTP_CREATED);
        $url = $this->generateUrl(
            'app_character_display',
            ['identifier' => $character->getIdentifier()]
        );
        $response->headers->set('Location', $url);
        return $response;
    }

    public function __construct(
        private CharacterServiceInterface $characterService
    ) {
    }
}
