<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $datePublication = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\Column(length: 100)]
    private ?string $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'article')]
    #[ORM\OrderBy(['dateCommentaire' => 'DESC'])]
    private Collection $commentaires;

    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'article', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $likes;

    #[ORM\Column(options: ['default' => false])]
    private bool $estApprouve = false;

    public function __construct()
    {
        $this->estApprouve = false;
        $this->commentaires = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime(); // Initialise aussi updatedAt au départ
    }
    

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
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

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDatePublication(): ?\DateTime
    {
        return $this->datePublication;
    }

    public function setDatePublication(?\DateTime $datePublication): static
    {
        $this->datePublication = $datePublication;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setArticle($this);
        }
        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            if ($commentaire->getArticle() === $this) {
                $commentaire->setArticle(null);
            }
        }
        return $this;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setArticle($this);
        }
        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getArticle() === $this) {
                $like->setArticle(null);
            }
        }
        return $this;
    }

    public function isEstApprouve(): ?bool
    {
    return $this->estApprouve;
    }

    public function setEstApprouve(bool $estApprouve): static
    {
    $this->estApprouve = $estApprouve;
    return $this;
    }
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $intro1 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $intro2 = null;

    public function getIntro1(): ?string
    {
        return $this->intro1;
    }

    public function setIntro1(?string $intro1): static
    {
        $this->intro1 = $intro1;
        return $this;
    }

    public function getIntro2(): ?string
    {
        return $this->intro2;
    }

    public function setIntro2(?string $intro2): static
    {
        $this->intro2 = $intro2;
        return $this;
    }
    #[ORM\Column(length: 255, nullable: true)]
private ?string $imageGauche = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageDroite = null;

    public function getImageGauche(): ?string { return $this->imageGauche; }
    public function setImageGauche(?string $imageGauche): static { $this->imageGauche = $imageGauche; return $this; }

    public function getImageDroite(): ?string { return $this->imageDroite; }
    public function setImageDroite(?string $imageDroite): static { $this->imageDroite = $imageDroite; return $this; }

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legendeImageGauche = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legendeImageDroite = null;

    public function getLegendeImageGauche(): ?string
    {
        return $this->legendeImageGauche;
    }

    public function setLegendeImageGauche(?string $legendeImageGauche): self
    {
        $this->legendeImageGauche = $legendeImageGauche;
        return $this;
    }

    public function getLegendeImageDroite(): ?string
    {
        return $this->legendeImageDroite;
    }

    public function setLegendeImageDroite(?string $legendeImageDroite): self
    {
        $this->legendeImageDroite = $legendeImageDroite;
        return $this;
    }

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageHeader = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legendeImageHeader = null;

    public function getImageHeader(): ?string
    {
        return $this->imageHeader;
    }

    public function setImageHeader(?string $imageHeader): static
    {
        $this->imageHeader = $imageHeader;
        return $this;
    }

    public function getLegendeImageHeader(): ?string
    {
        return $this->legendeImageHeader;
    }

    public function setLegendeImageHeader(?string $legendeImageHeader): static
    {
        $this->legendeImageHeader = $legendeImageHeader;
        return $this;
    }

    }

