<?php

namespace App\Service;

use DateTime;
use App\Entity\Building;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BuildingRepository;
use App\Form\BuildingType;
use LogicException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BuildingService implements BuildingServiceInterface
{
    public function __construct(
        private BuildingRepository $buildingRepository,
        private FormFactoryInterface $formFactory,
        private SluggerInterface $slugger,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
    ) {
    }

    public function create(string $data): Building
    {
        $building = new Building();

        $this->submit($building, BuildingType::class, $data);

        $building->setSlug($this->slugger->slug($building->getName())->lower());

        $building->setPrice(200);

        $building->setIdentifier(hash('sha1', uniqid()));
        $building->setCreation(new DateTime());
        $building->setModification(new DateTime());
        $this->isEntityFilled($building);

        $this->em->persist($building);
        $this->em->flush();

        return $building;
    }

    public function findAll(): array
    {
        $buildingsFinal = [];
        $buildings = $this->buildingRepository->findAll();
        foreach ($buildings as $building) {
            $buildingsFinal[] = $building->toArray();
        }
        return $buildingsFinal;
    }

    public function submit(Building $building, $formName, $data)
    {
        $dataArray = is_array($data) ? $data : json_decode($data, true);
        // Bad array
        if (null !== $data && !is_array($dataArray)) {
            throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . $data);
        }
        // Submits form
        $form = $this->formFactory->create($formName, $building, ['csrf_protection' => false]);
        $form->submit($dataArray, false);// With false, only submitted fields are validated
// Gets errors
        $errors = $form->getErrors();
        foreach ($errors as $error) {
            $errorMsg = 'Error ' . get_class($error->getCause());
            $errorMsg .= ' --> ' . $error->getMessageTemplate();
            $errorMsg .= ' ' . json_encode($error->getMessageParameters());
            throw new LogicException($errorMsg);
        }
    }

    public function update(Building $building, string $data): void
    {
        $this->submit($building, BuildingType::class, $data);
        $building->setSlug($this->slugger->slug($building->getName())->lower());
        $building->setModification(new DateTime());

        $this->isEntityFilled($building);

        $this->em->persist($building);
        $this->em->flush();
    }

    public function delete(Building $building): void
    {
        $this->em->remove($building);
        $this->em->flush();
    }

    public function isEntityFilled(Building $building)
    {
        // Vérification du bon fonctionnement en introduisant une erreur
        $building->setIdentifier('badidentifier'); // Supprimer par la suite
        $errors = $this->validator->validate($building);
        if (count($errors) > 0) {
            $errorMsg = (string) $errors . 'Wrong data for Entity -> ';
            $errorMsg .= json_encode($building->toArray());
            $errorMsg = 'Missing data for Entity -> ' . json_encode($building->toArray());
            throw new UnprocessableEntityHttpException($errorMsg);
        }
    }
}
