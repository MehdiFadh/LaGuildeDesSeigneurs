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
    ) {}
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
        $this->em->persist($character);
        $this->em->flush();
        return $character;
    }

}