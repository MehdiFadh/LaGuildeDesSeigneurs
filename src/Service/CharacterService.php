<?php

namespace App\Service;

// on ajoute le use pour supprimer le \ dans setCreation()
use App\Entity\Building;
use App\Entity\Character;
use App\Event\CharacterEvent;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CharacterService implements CharacterServiceInterface
{
    public function __construct(
        private CharacterRepository $characterRepository,
        private EntityManagerInterface $em,
        private FormFactoryInterface $formFactory,
        private ValidatorInterface $validator,
        private SluggerInterface $slugger,
        private EventDispatcherInterface $dispatcher,
        private SerializerInterface $serializer,
    ) {
    }

    // Creates the character
    public function create(string $data): Character
    {
        $character = new Character();
        $this->submit($character, CharacterType::class, $data);
        // Dispatch created event
        $event = new CharacterEvent($character);
        // Utilisation de la constante définie dans l'Event
        $this->dispatcher->dispatch($event, CharacterEvent::CHARACTER_CREATED);
        $character->setSlug($this->slugger->slug($character->getName())->lower());
        $character->setIdentifier(hash('sha1', uniqid()));
        $character->setCreation(new \DateTime());
        $character->setModification(new \DateTime());
        $this->isEntityFilled($character);
        $this->em->persist($character);
        $this->em->flush();
        // Dispatch created post database event
        $this->dispatcher->dispatch($event, CharacterEvent::CHARACTER_CREATED_POST_DATABASE);

        return $character;
    }

    // Finds all the characters
    public function findAll(): array
    {
        return $this->characterRepository->findAll();
    }

    public function update(Character $character, string $data): void
    {
        $this->submit($character, CharacterType::class, $data);
        // Dispatch updated event
        $event = new CharacterEvent($character);
        $this->dispatcher->dispatch($event, CharacterEvent::CHARACTER_UPDATED);
        $character->setSlug($this->slugger->slug($character->getName())->lower());

        $character->setModification(new \DateTime());
        // $character->setIdentifier(hash('sha1', uniqid())) -> supprimé pour ne pas le changer
        $this->isEntityFilled($character);
        $this->em->persist($character);
        $this->em->flush();
    }

    // Submits the form
    public function submit(Character $character, $formName, $data)
    {
        $dataArray = is_array($data) ? $data : json_decode($data, true);
        // Bad array
        if (null !== $data && !is_array($dataArray)) {
            throw new UnprocessableEntityHttpException('Submitted data is not an array -> '.$data);
        }
        // Submits form
        $form = $this->formFactory->create($formName, $character, ['csrf_protection' => false]);
        $form->submit($dataArray, false); // With false, only submitted fields are validated
        // Gets errors
        $errors = $form->getErrors();
        foreach ($errors as $error) {
            $errorMsg = 'Error '.get_class($error->getCause());
            $errorMsg .= ' --> '.$error->getMessageTemplate();
            $errorMsg .= ' '.json_encode($error->getMessageParameters());
            throw new \LogicException($errorMsg);
        }
    }

    public function delete(Character $character): void
    {
        $this->em->remove($character);
        $this->em->flush();
    }

    // Checks if the entity has been well filled
    public function isEntityFilled(Character $character)
    {
        // Vérification du bon fonctionnement en introduisant une erreur
        $errors = $this->validator->validate($character);
        if (count($errors) > 0) {
            $errorMsg = (string) $errors.'Wrong data for Entity -> ';
            $errorMsg .= json_encode($this->serializeJson($character));
            throw new UnprocessableEntityHttpException($errorMsg);
        }
    }

    // Serializes the object(s)
    public function serializeJson($object)
    {
        $context = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, array $context): string {
                if ($object instanceof Building || $object instanceof Character) {
                    return $object->getIdentifier();
                }
                throw new CircularReferenceException('A circular reference has been detected when serializing the object of class "'.get_debug_type($object).'".');
            },
        ];

        return $this->serializer->serialize($object, 'json', $context);
    }
}
