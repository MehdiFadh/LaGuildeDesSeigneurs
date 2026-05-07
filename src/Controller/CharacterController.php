<?php

namespace App\Controller;

use App\Entity\Character;
use App\Service\CharacterServiceInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

final class CharacterController extends AbstractController
{
    #[Route('/characters/', name: 'app_character_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns an array of Characters',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Character::class))
        )
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied'
    )]
    #[OA\Tag(name: 'Character')]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'Number of the page',
        schema: new OA\Schema(type: 'integer', default: 1),
        required: true
    )]
    #[OA\Parameter(
        name: 'size',
        in: 'query',
        description: 'Number of records',
        schema: new OA\Schema(type: 'integer', default: 10, minimum: 1, maximum: 100),
        required: true
    )]
    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function index(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('characterIndex', null);
        $characters = $this->characterService->findAllPaginated($request->query);

        return JsonResponse::fromJsonString($this->characterService->serializeJson($characters));
    }

    #[Route('/characters/{identifier}', requirements: ['identifier' => '^([a-z0-9]{40})$'], name: 'app_character_display', methods: ['GET'])]
    // DISPLAY
    #[OA\Parameter(
        name: 'identifier',
        in: 'path',
        description: 'Identifier for the Character',
        schema: new OA\Schema(type: 'string'),
        required: true
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the Character',
        content: new OA\JsonContent(ref: new Model(type: Character::class))
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[OA\Tag(name: 'Character')]
    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function display(
        #[MapEntity(expr: 'repository.findOneByIdentifier(identifier)')]
        Character $character,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('characterDisplay', $character);

        return JsonResponse::fromJsonString($this->characterService->serializeJson($character));
    }

    #[Route('/characters/', name: 'app_character_create', methods: ['POST'])]
    // "methods: ['POST']" permet d'interdire GET pour la création
    #[OA\RequestBody(
        request: 'Character',
        description: 'Data for the Character',
        required: true,
        content: new OA\JsonContent(
            type: Character::class,
            example: [
                'kind' => 'Dame',
                'name' => 'Anardil',
                'surname' => 'Amie du soleil',
                'caste' => 'Magicien',
                'knowledge' => 'Sciences',
                'intelligence' => 180,
                'strength' => 180,
                'image' => '/dames/anardil.webp',
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns the Character',
        content: new Model(type: Character::class)
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied'
    )]
    #[OA\Tag(name: 'Character')]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('characterCreate', null);
        $character = $this->characterService->create($request->getContent());
        $response = JsonResponse::fromJsonString($this->characterService->serializeJson($character), JsonResponse::HTTP_CREATED);
        $url = $this->generateUrl(
            'app_character_display',
            ['identifier' => $character->getIdentifier()]
        );
        $response->headers->set('Location', $url);

        return $response;
    }

    #[Route('/characters/{identifier:character}',
        requirements: ['identifier' => '^([a-z0-9]{40})$'],
        name: 'app_character_update',
        methods: ['PUT'])]

    #[OA\Parameter(
        name: 'identifier',
        in: 'path',
        description: 'Identifier for the Character',
        schema: new OA\Schema(type: 'string'),
        required: true
    )]
    #[OA\RequestBody(
        request: 'Character',
        description: 'Data for the Character',
        required: true,
        content: new OA\JsonContent(
            type: Character::class,
            example: [
                'kind' => 'Seigneur',
                'name' => 'Gorthol',
            ]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'No content'
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[OA\Tag(name: 'Character')]
    public function update(Request $request, Character $character): JsonResponse
    {
        $this->denyAccessUnlessGranted('characterUpdate', $character);
        $this->characterService->update($character, $request->getContent());

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/characters/{identifier:character}',
        requirements: ['identifier' => '^([a-z0-9]{40})$'],
        name: 'app_character_delete',
        methods: ['DELETE'])]

    #[OA\Parameter(
        name: 'identifier',
        in: 'path',
        description: 'Identifier for the Character',
        schema: new OA\Schema(type: 'string'),
        required: true
    )]
    #[OA\Response(
        response: 204,
        description: 'No content'
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[OA\Tag(name: 'Character')]
    public function delete(Character $character)
    {
        $this->denyAccessUnlessGranted('characterDelete', $character);
        $this->characterService->delete($character);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    // IMAGES
    #[Route(
        '/characters/images/{number}',
        name: 'app_character_images',
        requirements: ['number' => '^([0-9]{1,2})$'],
        methods: ['GET']
    )]
    #[OA\Parameter(
        name: 'number',
        in: 'path',
        description: 'Number of images',
        schema: new OA\Schema(type: 'integer'),
        required: false
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns links for images'
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied'
    )]
    #[OA\Tag(name: 'Character')]
    public function images(int $number = 1): JsonResponse
    {
        $this->denyAccessUnlessGranted('characterIndex', null);
        $images = $this->characterService->getImages($number);

        return new JsonResponse($images);
    }

    #[Route(
        '/characters/images/{kind}/{number}',
        name: 'app_character_images_kind',
        requirements: [
            'number' => '^([0-9]{1,2})$',
            'kind' => '^(dames|seigneurs|tourmenteurs|tourmenteuses)$',
        ],
        methods: ['GET']
    )]
    #[OA\Parameter(
        name: 'number',
        in: 'path',
        description: 'Number of images',
        schema: new OA\Schema(type: 'integer'),
        required: false
    )]
    #[OA\Parameter(
        name: 'kind',
        in: 'path',
        description: 'Kind of Character',
        example: 'dames|seigneurs|tourmenteurs|tourmenteuses',
        schema: new OA\Schema(type: 'string'),
        required: false
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns links for images'
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied'
    )]
    #[OA\Tag(name: 'Character')]
    public function imagesKind(string $kind, int $number = 1): JsonResponse
    {
        $this->denyAccessUnlessGranted('characterIndex', null);
        $images = $this->characterService->getImagesKind($kind, $number);

        return new JsonResponse($images);
    }

    public function __construct(
        private CharacterServiceInterface $characterService,
    ) {
    }
}
