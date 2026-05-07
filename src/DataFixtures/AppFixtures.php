<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Character;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;



class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $users = $this->createUsers($manager);
        $jsonBuildings = $this->createJsonBuildings($manager);
        $this->createJsonCharacters($manager, $jsonBuildings, $users);
        // Les Buildings DOIVENT être faits en premier pour pouvoir être liés aux Characters
        $randomBuildings = $this->createRandomBuildings($manager);
        $this->createRandomCharacters($manager, $randomBuildings);
    }

    public function createRandomCharacters(ObjectManager $manager, $randomBuildings): void
    {
        $totalCharacters = 20;
        for ($i = 0; $i < $totalCharacters; ++$i) {
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
            // Un Building aléatoire sera envoyé
            $character->setBuilding($randomBuildings[array_rand($randomBuildings)]);
            $character->setCreation(new \DateTime());
            $character->setModification(new \DateTime());
            $manager->persist($character);
        }
        $manager->flush();
    }

    public function createJsonCharacters(ObjectManager $manager, $jsonBuildings, $users): void
    {
        $characters = json_decode(file_get_contents('https://la-guilde-des-seigneurs.com/json/characters.json'), true);
        $charactersArray = [];
        foreach ($characters as $characterData) {
            $character = $this->setCharacter($characterData);
            $character->setUtilisateur($users[array_rand($users)]);
            $manager->persist($character);
            $charactersArray[] = $character;
        }
        foreach ($jsonBuildings as $buildingData) {
            $building = $this->setBuilding($buildingData);
            // Characters
            foreach ($charactersArray as $character) {
                if ($building->getCaste() === $character->getCaste()) {
                    $building->addCharacter($character);
                }
            }
            $manager->persist($building);
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
        private UserPasswordHasherInterface $hasher,
        private SluggerInterface $slugger,
    ) {
    }

    public function createJsonBuildings(ObjectManager $manager): array
    {
        $buildings = json_decode(file_get_contents('https://la-guilde-des-seigneurs.com/json/buildings.json'), true);
        foreach ($buildings as $buildingData) {
            $manager->persist($this->setBuilding($buildingData));
        }
        $manager->flush();

        return $buildings;
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

    public function createRandomBuildings(ObjectManager $manager): array
    {
        $buildings = [];
        $totalBuildings = 5;
        for ($i = 0; $i < $totalBuildings; ++$i) {
            $building = new Building();
            $building->setName('Château ' . $i);
            $building->setSlug('chateau-' . $i);
            $building->setCaste('Guerrier ' . $i);
            $building->setStrength(rand(0, 2000));
            $building->setImage('/buildings/chateau-' . strtolower($building->getName()) . '.webp');
            $building->setIdentifier(hash('sha1', uniqid()));
            $building->setCreation(new \DateTime());
            $building->setModification(new \DateTime());
            $manager->persist($building);
            // Used to link to Characters
            $buildings[] = $building;
        }
        $manager->flush();

        return $buildings;
    }

    // Creates Users
    public function createUsers(ObjectManager $manager): array
    {
        $emails = [
            'contact@example.com',
            'info@example.com',
            'email@example.com',
        ];
        $users = [];
        foreach ($emails as $email) {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword($this->hasher->hashPassword($user, 'StrongPassword*'));
            $user->setCreation(new \DateTime());
            $user->setModification(new \DateTime());
            // On définit seulement cet utilisateur comme admin
            if ('contact@example.com' === $email) {
                $user->setRoles(['ROLE_ADMIN']);
            }
            $manager->persist($user);
            $users[] = $user;
        }
        $manager->flush();
        return $users;
    }
}
