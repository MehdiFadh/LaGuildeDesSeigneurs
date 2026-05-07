<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\UserServiceInterface;
use OpenApi\Attributes as OA;

final class UserController extends AbstractController
{

    #[Route('
        /signin',
        name: 'app_signin',
        methods: ['POST']
    )]
    #[OA\RequestBody(
        request: "User",
        description: "Data for the User",
        required: true,
        content: new OA\JsonContent(
            type: User::class,
            example: [
                "username" => "contact@example.com",
                "password" => "StrongPassword*"
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns a JWT',
        content: new OA\JsonContent(
            type: 'string',
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[OA\Tag(name: 'User')]
    public function signin(): JsonResponse
    {
        $user = $this->getUser();
        if (null !== $user) {
            return new JsonResponse([
                'token' => $this->userService->getToken($user),
            ]);
        }

        return new JsonResponse([
            'error' => 'User not found',
        ]);
    }

    public function __construct(
        private UserServiceInterface $userService
    ) {
    }
}
