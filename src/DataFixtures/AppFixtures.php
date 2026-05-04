<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Character;
use App\Entity\Building;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $this->createRandomCharacters($manager);
        $this->createJsonCharacters($manager);
        $this->createJsonBuildings($manager);
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

    public function createJsonBuildings(ObjectManager $manager){
        $buildings = json_decode(file_get_contents('https://la-guilde-des-seigneurs.com/json/buildings.json'), true);
        foreach ($buildings as $buildingData) {
            $manager->persist($this->setBuilding($buildingData));
        }
        $manager->flush();
    }

    public function setBuilding(array $buildingData): Building
    {
        $building = new Building();
        foreach ($buildingData as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($building, $method)) {
                $building->$method($value ?? null);
            }
        }
        $building->setSlug($this->slugger->slug($buildingData['name'])->lower());
        $building->setIdentifier(hash('sha1', uniqid()));
        $building->setCreation(new \DateTime());
        $building->setModification(new \DateTime());
        
        // Random stars if not provided
        if (!isset($buildingData['stars'])) {
            $building->setStars(rand(1, 5));
        }

        return $building;
    }
}
