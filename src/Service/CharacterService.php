<?php
namespace App\Service;
use DateTime; // on ajoute le use pour supprimer le \ dans setCreation()
use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CharacterRepository;
class CharacterService implements CharacterServiceInterface
{

    public function __construct(
        private CharacterRepository $characterRepository,
        private EntityManagerInterface $em,
    ) {
    }
    // Creates the character
    public function create(): Character
    {
        $character = new Character();
        $character->setKind('Dame');
        $character->setName('Anardil');
        $character->setSlug('anardil');
        $character->setSurname('Amie du soleil');
        $character->setCaste('Magicien');
        $character->setKnowledge('Sciences');
        $character->setIntelligence(180);
        $character->setStrength(180);
        $character->setIdentifier(hash('sha1', uniqid()));
        $character->setImage('/dames/anardil.webp');
        $character->setCreation(new DateTime());
        $character->setModification(new DateTime());
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

    public function update(Character $character): void
    {
        $character->setKind('Seigneur');
        $character->setName('Gorthol');
        $character->setSlug('gorthol');
        $character->setSurname('Heaume de terreur');
        $character->setCaste('Chevalier');
        $character->setKnowledge('Diplomatie');
        $character->setIntelligence(140);
        $character->setStrength(140);
        $character->setImage('/seigneurs/gorthol.webp');
        $character->setModification(new DateTime());
        // $character->setIdentifier(hash('sha1', uniqid())) -> supprimé pour ne pas le changer
        $this->em->persist($character);
        $this->em->flush();
    }

    public function delete(Character $character): void
    {
        $this->em->remove($character);
        $this->em->flush();
    }

}