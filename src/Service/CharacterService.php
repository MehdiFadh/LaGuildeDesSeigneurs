<?php
namespace App\Service;
use DateTime; // on ajoute le use pour supprimer le \ dans setCreation()
use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CharacterRepository;
use App\Form\CharacterType;
use LogicException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
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
    ) {
    }
    // Creates the character
    public function create(string $data): Character
    {
        $character = new Character();
        $this->submit($character, CharacterType::class, $data);
        $character->setSlug($this->slugger->slug($character->getName())->lower());
        $character->setIdentifier(hash('sha1', uniqid()));
        $character->setCreation(new DateTime());
        $character->setModification(new DateTime());
        $this->isEntityFilled($character);
        $this->em->persist($character);
        $this->em->flush();
        return $character;
    }

    // Finds all the characters
    public function findAll(): array
    {
        $charactersFinal = [];
        $characters = $this->characterRepository->findAll();
        foreach ($characters as $character) {
            $charactersFinal[] = $character->toArray();
        }
        return $charactersFinal;
    }

    public function update(Character $character, string $data): void
    {
        $this->submit($character, CharacterType::class, $data);
        $character->setSlug($this->slugger->slug($character->getName())->lower());


        $character->setModification(new DateTime());
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
            throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . $data);
        }
        // Submits form
        $form = $this->formFactory->create($formName, $character, ['csrf_protection' => false]);
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

    public function delete(Character $character): void
    {
        $this->em->remove($character);
        $this->em->flush();
    }

    // Checks if the entity has been well filled
    public function isEntityFilled(Character $character)
    {
        // Vérification du bon fonctionnement en introduisant une erreur
        $character->setIdentifier('badidentifier'); // Supprimer par la suite
        $errors = $this->validator->validate($character);
        if (count($errors) > 0) {
            $errorMsg = (string) $errors . 'Wrong data for Entity -> ';
            $errorMsg .= json_encode($character->toArray());
            throw new UnprocessableEntityHttpException($errorMsg);
        }
    }

}