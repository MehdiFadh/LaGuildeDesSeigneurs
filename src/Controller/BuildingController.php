<?php

// src/Controller/BuildingController.php
// Contrôleur de l'API gérant les requêtes HTTP pour les bâtiments (GET, POST, PUT, DELETE) et retournant des réponses JSON.

namespace App\Controller;

use App\Entity\Building;
use App\Service\BuildingServiceInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

final class BuildingController extends AbstractController
{
    #[Route('/buildings/', name: 'app_building_index', methods: ['GET'])]
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
        $this->denyAccessUnlessGranted('buildingIndex', null);
        $buildings = $this->buildingService->findAllPaginated($request->query);

        return JsonResponse::fromJsonString($this->buildingService->serializeJson($buildings));
    }

    #[Route('/buildings/{identifier}', requirements: ['identifier' => '^([a-z0-9]{40})$'], name: 'app_building_display', methods: ['GET'])]
    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function display(
        #[MapEntity(expr: 'repository.findOneByIdentifier(identifier)')]
        Building $building,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('buildingDisplay', $building);

        // On est toujours dans la classe JsonResponse
        // Mais on l'utilise de manière statique
        // d'où l'utilisation des ::
        // et on appelle la méthode fromJsonString()
        return JsonResponse::fromJsonString($this->buildingService->serializeJson($building));
    }

    #[Route('/buildings/', name: 'app_building_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('buildingCreate', null);
        $building = $this->buildingService->create($request->getContent());
        $response = JsonResponse::fromJsonString($this->buildingService->serializeJson($building), JsonResponse::HTTP_CREATED);
        $url = $this->generateUrl(
            'app_building_display',
            ['identifier' => $building->getIdentifier()]
        );
        $response->headers->set('Location', $url);

        return $response;
    }

    #[Route('/buildings/{identifier:building}', requirements: ['identifier' => '^([a-z0-9]{40})$'], name: 'app_building_update', methods: ['PUT'])]
    public function update(Request $request, Building $building): JsonResponse
    {
        $this->denyAccessUnlessGranted('buildingUpdate', $building);
        $this->buildingService->update($building, $request->getContent());

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/buildings/{identifier:building}', requirements: ['identifier' => '^([a-z0-9]{40})$'], name: 'app_building_delete', methods: ['DELETE'])]
    public function delete(Building $building): JsonResponse
    {
        $this->denyAccessUnlessGranted('buildingDelete', $building);
        $this->buildingService->delete($building);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route(
        '/buildings/images/{number}',
        name: 'app_building_images',
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
    #[OA\Tag(name: 'Building')]
    public function images(int $number = 1): JsonResponse
    {
        $this->denyAccessUnlessGranted('buildingIndex', null);
        $images = $this->buildingService->getImages($number);

        return new JsonResponse($images);
    }

    public function __construct(
        private BuildingServiceInterface $buildingService,
    ) {
    }
}
