<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`', uniqueConstraints: [
new ORM\UniqueConstraint(name: 'unique_like', columns: ['user_id', 'article_id'])
])]
#[UniqueEntity(fields: ['user', 'article'], message: 'Vous avez déjà liké cet article.')]
#[ORM\HasLifecycleCallbacks]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $dateLike = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateLike(): ?\DateTime
    {
        return $this->dateLike;
    }

    public function setDateLike(\DateTime $dateLike): static
    {
        $this->dateLike = $dateLike;
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

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {

        $this->article = $article;

        return $this;
    }
    #[ORM\PrePersist]
    public function setDateAutomatically(): void
    {
        if ($this->dateLike === null) {
            $this->dateLike = new \DateTime();
        }
    }
}

