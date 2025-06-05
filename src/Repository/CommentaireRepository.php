<?php


namespace App\Repository;

use App\Entity\Commentaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaire>
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

    public function countCommentairesDepuis(\DateTimeInterface $date): int
    {
    return $this->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->where('c.dateCommentaire >= :date')
        ->setParameter('date', $date)
        ->getQuery()
        ->getSingleScalarResult();
    }
    public function countCommentairesDerniersJours(int $jours = 7): int
    {
    $date = new \DateTimeImmutable("-{$jours} days");

    return $this->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->where('c.dateCommentaire >= :date')
        ->setParameter('date', $date)
        ->getQuery()
        ->getSingleScalarResult();
    }



//    /**
//     * @return Commentaire[] Returns an array of Commentaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commentaire
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
