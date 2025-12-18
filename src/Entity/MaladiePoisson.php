<?php

namespace App\Entity;

use App\Repository\MaladiePoissonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaladiePoissonRepository::class)]
class MaladiePoisson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $agentPathogene = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $symptomes = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $causes = null;

    #[ORM\Column(length: 100)]
    private ?string $gravite = null;

    #[ORM\Column]
    private ?bool $contagieuse = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $traitement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $prevention = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dureeTraitement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAgentPathogene(): ?string
    {
        return $this->agentPathogene;
    }

    public function setAgentPathogene(?string $agentPathogene): static
    {
        $this->agentPathogene = $agentPathogene;

        return $this;
    }

    public function getSymptomes(): ?string
    {
        return $this->symptomes;
    }

    public function setSymptomes(string $symptomes): static
    {
        $this->symptomes = $symptomes;

        return $this;
    }

    public function getCauses(): ?string
    {
        return $this->causes;
    }

    public function setCauses(string $causes): static
    {
        $this->causes = $causes;

        return $this;
    }

    public function getGravite(): ?string
    {
        return $this->gravite;
    }

    public function setGravite(string $gravite): static
    {
        $this->gravite = $gravite;

        return $this;
    }

    public function isContagieuse(): ?bool
    {
        return $this->contagieuse;
    }

    public function setContagieuse(bool $contagieuse): static
    {
        $this->contagieuse = $contagieuse;

        return $this;
    }

    public function getTraitement(): ?string
    {
        return $this->traitement;
    }

    public function setTraitement(string $traitement): static
    {
        $this->traitement = $traitement;

        return $this;
    }

    public function getPrevention(): ?string
    {
        return $this->prevention;
    }

    public function setPrevention(?string $prevention): static
    {
        $this->prevention = $prevention;

        return $this;
    }

    public function getDureeTraitement(): ?string
    {
        return $this->dureeTraitement;
    }

    public function setDureeTraitement(?string $dureeTraitement): static
    {
        $this->dureeTraitement = $dureeTraitement;

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
}
