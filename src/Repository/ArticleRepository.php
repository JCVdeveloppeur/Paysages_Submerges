<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Sidebar : derniers articles validés
     */
    public function findLatestPublished(int $limit = 3): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.estApprouve = :approved')
            ->setParameter('approved', true)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * À lire aussi : mêmes catégorie, exclus l’article courant
     */
    public function findRelatedByCategory(string $category, int $excludeId, int $limit = 3): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.estApprouve = true')
            ->andWhere('a.categorie = :cat')
            ->andWhere('a.id != :id')
            ->setParameter('cat', $category)
            ->setParameter('id', $excludeId)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Navigation : article précédent (publié avant celui-ci)
     */
    public function findPrevPublished(\DateTimeInterface $createdAt): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.estApprouve = true')
            ->andWhere('a.createdAt < :d')
            ->setParameter('d', $createdAt)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Navigation : article suivant (publié après celui-ci)
     */
    public function findNextPublished(\DateTimeInterface $createdAt): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.estApprouve = true')
            ->andWhere('a.createdAt > :d')
            ->setParameter('d', $createdAt)
            ->orderBy('a.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
