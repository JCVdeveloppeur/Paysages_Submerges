<?php

namespace App\Entity;

use App\Repository\PlanteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanteRepository::class)]
class Plante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomCommun = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomScientifique = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $famille = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $origine = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $biotope = null;

    public function getBiotope(): ?string
    {
        return $this->biotope;
    }

    public function setBiotope(?string $biotope): static
    {
        $this->biotope = $biotope;
        return $this;
    }

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $eclairage = null;

    #[ORM\Column(length: 100)]
    private ?string $croissance = null;

    #[ORM\Column]
    private ?int $hauteurMax = null;

    #[ORM\Column(length: 100)]
    private ?string $positionAquarium = null;

    #[ORM\Column(length: 100)]
    private ?string $difficulte = null;

    #[ORM\Column(nullable: true)]
    private ?float $phMin = null;

    #[ORM\Column(nullable: true)]
    private ?float $phMax = null;

    #[ORM\Column(nullable: true)]
    private ?float $tempMin = null;

    #[ORM\Column(nullable: true)]
    private ?float $tempMax = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $no = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCommun(): ?string
    {
        return $this->nomCommun;
    }

    public function setNomCommun(?string $nomCommun): static
    {
        $this->nomCommun = $nomCommun;

        return $this;
    }

    public function getNomScientifique(): ?string
    {
        return $this->nomScientifique;
    }

    public function setNomScientifique(?string $nomScientifique): static
    {
        $this->nomScientifique = $nomScientifique;

        return $this;
    }

    public function getFamille(): ?string
    {
        return $this->famille;
    }

    public function setFamille(?string $famille): static
    {
        $this->famille = $famille;

        return $this;
    }

    public function getOrigine(): ?string
    {
        return $this->origine;
    }

    public function setOrigine(?string $origine): static
    {
        $this->origine = $origine;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEclairage(): ?string
    {
        return $this->eclairage;
    }

    public function setEclairage(string $eclairage): static
    {
        $this->eclairage = $eclairage;

        return $this;
    }

    public function getCroissance(): ?string
    {
        return $this->croissance;
    }

    public function setCroissance(string $croissance): static
    {
        $this->croissance = $croissance;

        return $this;
    }

    public function getHauteurMax(): ?int
    {
        return $this->hauteurMax;
    }

    public function setHauteurMax(int $hauteurMax): static
    {
        $this->hauteurMax = $hauteurMax;

        return $this;
    }

    public function getPositionAquarium(): ?string
    {
        return $this->positionAquarium;
    }

    public function setPositionAquarium(string $positionAquarium): static
    {
        $this->positionAquarium = $positionAquarium;

        return $this;
    }

    public function getDifficulte(): ?string
    {
        return $this->difficulte;
    }

    public function setDifficulte(string $difficulte): static
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    public function getPhMin(): ?float
    {
        return $this->phMin;
    }

    public function setPhMin(?float $phMin): static
    {
        $this->phMin = $phMin;

        return $this;
    }

    public function getPhMax(): ?float
    {
        return $this->phMax;
    }

    public function setPhMax(?float $phMax): static
    {
        $this->phMax = $phMax;

        return $this;
    }

    public function getTempMin(): ?float
    {
        return $this->tempMin;
    }

    public function setTempMin(?float $tempMin): static
    {
        $this->tempMin = $tempMin;

        return $this;
    }

    public function getTempMax(): ?float
    {
        return $this->tempMax;
    }

    public function setTempMax(?float $tempMax): static
    {
        $this->tempMax = $tempMax;

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

    public function getNo(): ?string
    {
        return $this->no;
    }

    public function setNo(?string $no): static
    {
        $this->no = $no;

        return $this;
    }
}
