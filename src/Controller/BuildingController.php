<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Building;
use App\Service\BuildingServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

final class BuildingController extends AbstractController
{
    #[Route('/buildings/', name: 'app_building_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $this->denyAccessUnlessGranted('buildingIndex', null);
        $buildings = $this->buildingService->findAll();
        return JsonResponse::fromJsonString($this->buildingService->serializeJson($buildings));
    }

    #[Route('/buildings/{identifier}', requirements: ['identifier' => '^([a-z0-9]{40})$'], name: 'app_building_display', methods: ['GET'])]
    public function display(
        #[MapEntity(expr: 'repository.findOneByIdentifier(identifier)')]
        Building $building
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

    public function __construct(
        private BuildingServiceInterface $buildingService
    ) {
    }
}
