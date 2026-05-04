<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Character;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $this->createRandomCharacters($manager);
        $this->createJsonCharacters($manager);
    }

    public function createRandomCharacters(ObjectManager $manager): void
    {
        $totalCharacters = 20;
        for ($i = 0; $i < $totalCharacters; $i++) {
            $character = new Character();
            $character->setKind(rand(0, 1) ? 'Dame' : 'Seigneur');
            $character->setName('Anardil' . $i);
            $character->setSlug('anardil' . $i);
            $character->setSurname('Amie du soleil');
            $character->setCaste('Magicien');
            $character->setKnowledge('Sciences');
            $character->setIntelligence(mt_rand(100, 200));
            $character->setStrength(mt_rand(100, 200));
            $character->setIdentifier(hash('sha1', uniqid()));
            $character->setImage('/' . strtolower($character->getKind()) . 's/' . strtolower($character->getKind()) . '.webp');
            $character->setCreation(new \DateTime());
            $character->setModification(new \DateTime());
            $manager->persist($character);
        }
        $manager->flush();
    }

    public function createJsonCharacters(ObjectManager $manager){
        $characters = json_decode(file_get_contents('https://la-guilde-des-seigneurs.com/json/characters.json'), true);
        foreach ($characters as $characterData) {
            $manager->persist($this->setCharacter($characterData));
        }
        $manager->flush();
    }

    // Sets the Character with its data
    public function setCharacter(array $characterData): Character
    {
        $character = new Character();
        foreach ($characterData as $key => $value) {
            $method = 'set' . ucfirst($key); // Construit le nom de la méthode
            if (method_exists($character, $method)) { // Si la méthode existe
            $character->$method($value ?? null); // Appelle la méthode
            }
        }
        $character->setSlug($this->slugger->slug($characterData['name'])->lower());
        $character->setIdentifier(hash('sha1', uniqid()));
        $character->setCreation(new \DateTime());
        $character->setModification(new \DateTime());
        return $character;
    }

    public function __construct(
        private SluggerInterface $slugger,
    ){
        
    }
}
