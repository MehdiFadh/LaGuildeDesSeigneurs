<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = 1;

    #[ORM\Column(length: 20)]
    private ?string $name = 'Anardil';

    #[ORM\Column(length: 50)]
    private ?string $surname = 'Amie du soleil';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $caste = 'Magicien';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $knowledge = 'Sciences';

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $intelligence = 180;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $strength = 180;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $image = '/dames/anardil.webp';

    #[ORM\Column]
    private ?\DateTime $creation = null;

    #[ORM\Column(length: 20)]
    private ?string $slug = null;

    #[ORM\Column(length: 20)]
    private ?string $kind = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getCaste(): ?string
    {
        return $this->caste;
    }

    public function setCaste(?string $caste): static
    {
        $this->caste = $caste;

        return $this;
    }

    public function getKnowledge(): ?string
    {
        return $this->knowledge;
    }

    public function setKnowledge(?string $knowledge): static
    {
        $this->knowledge = $knowledge;

        return $this;
    }

    public function getIntelligence(): ?int
    {
        return $this->intelligence;
    }

    public function setIntelligence(?int $intelligence): static
    {
        $this->intelligence = $intelligence;

        return $this;
    }

    public function getStrength(): ?int
    {
        return $this->strength;
    }

    public function setStrength(?int $strength): static
    {
        $this->strength = $strength;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function getCreation(): ?\DateTime
    {
        return $this->creation;
    }

    public function setCreation(\DateTime $creation): static
    {
        $this->creation = $creation;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(string $kind): static
    {
        $this->kind = $kind;

        return $this;
    }

}
