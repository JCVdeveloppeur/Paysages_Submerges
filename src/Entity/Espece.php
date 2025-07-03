<?php

namespace App\Entity;

use App\Repository\EspeceRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EspeceRepository::class)]
class Espece
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomCommun = null;

    #[ORM\Column(length: 255)]
    private ?string $nomScientifique = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $classification = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $origine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $repartitionGeographique = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionPhysique = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dimorphismeSexuel = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $alimentation = null;

    #[ORM\Column(nullable: true)]
    private ?int $tailleMinimaleBac = null;

    #[ORM\Column(nullable: true)]
    private ?float $temperatureMin = null;

    #[ORM\Column(nullable: true)]
    private ?float $temperatureMax = null;

    #[ORM\Column(nullable: true)]
    private ?float $phMin = null;

    #[ORM\Column(nullable: true)]
    private ?float $phMax = null;

    #[ORM\Column(nullable: true)]
    private ?float $ghMin = null;

    #[ORM\Column(nullable: true)]
    private ?float $ghMax = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comportement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reproduction = null;

    #[ORM\Column(length: 50)]
    private ?string $typeEspece = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $biotope = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dureeVie = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCommun(): ?string
    {
        return $this->nomCommun;
    }

    public function setNomCommun(string $nomCommun): static
    {
        $this->nomCommun = $nomCommun;

        return $this;
    }

    public function getNomScientifique(): ?string
    {
        return $this->nomScientifique;
    }

    public function setNomScientifique(string $nomScientifique): static
    {
        $this->nomScientifique = $nomScientifique;

        return $this;
    }

    public function getClassification(): ?string
    {
        return $this->classification;
    }

    public function setClassification(?string $classification): static
    {
        $this->classification = $classification;

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

    public function getRepartitionGeographique(): ?string
    {
        return $this->repartitionGeographique;
    }

    public function setRepartitionGeographique(?string $repartitionGeographique): static
    {
        $this->repartitionGeographique = $repartitionGeographique;

        return $this;
    }

    public function getDescriptionPhysique(): ?string
    {
        return $this->descriptionPhysique;
    }

    public function setDescriptionPhysique(?string $descriptionPhysique): static
    {
        $this->descriptionPhysique = $descriptionPhysique;

        return $this;
    }

    public function getDimorphismeSexuel(): ?string
    {
        return $this->dimorphismeSexuel;
    }

    public function setDimorphismeSexuel(?string $dimorphismeSexuel): static
    {
        $this->dimorphismeSexuel = $dimorphismeSexuel;

        return $this;
    }

    public function getAlimentation(): ?string
    {
        return $this->alimentation;
    }

    public function setAlimentation(?string $alimentation): static
    {
        $this->alimentation = $alimentation;

        return $this;
    }

    public function getTailleMinimaleBac(): ?int
    {
        return $this->tailleMinimaleBac;
    }

    public function setTailleMinimaleBac(?int $tailleMinimaleBac): static
    {
        $this->tailleMinimaleBac = $tailleMinimaleBac;

        return $this;
    }

    public function getTemperatureMin(): ?float
    {
        return $this->temperatureMin;
    }

    public function setTemperatureMin(?float $temperatureMin): static
    {
        $this->temperatureMin = $temperatureMin;

        return $this;
    }

    public function getTemperatureMax(): ?float
    {
        return $this->temperatureMax;
    }

    public function setTemperatureMax(?float $temperatureMax): static
    {
        $this->temperatureMax = $temperatureMax;

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

    public function getGhMin(): ?float
    {
        return $this->ghMin;
    }

    public function setGhMin(?float $ghMin): static
    {
        $this->ghMin = $ghMin;

        return $this;
    }

    public function getGhMax(): ?float
    {
        return $this->ghMax;
    }

    public function setGhMax(?float $ghMax): static
    {
        $this->ghMax = $ghMax;

        return $this;
    }

    public function getComportement(): ?string
    {
        return $this->comportement;
    }

    public function setComportement(?string $comportement): static
    {
        $this->comportement = $comportement;

        return $this;
    }

    public function getReproduction(): ?string
    {
        return $this->reproduction;
    }

    public function setReproduction(?string $reproduction): static
    {
        $this->reproduction = $reproduction;

        return $this;
    }

    public function getTypeEspece(): ?string
    {
        return $this->typeEspece;
    }

    public function setTypeEspece(string $typeEspece): static
    {
        $this->typeEspece = $typeEspece;

        return $this;
    }

    public function getBiotope(): ?string
    {
        return $this->biotope;
    }

    public function setBiotope(?string $biotope): static
    {
        $this->biotope = $biotope;

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
    

    public function getDureeVie(): ?string
    {
    return $this->dureeVie;
    }

    public function setDureeVie(?string $dureeVie): static
    {
    $this->dureeVie = $dureeVie;
    return $this;
    }

    }
