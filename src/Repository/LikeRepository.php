<?php
// Existant
namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function countLikesDepuis(\DateTimeInterface $date): int
    {
    return $this->createQueryBuilder('l')
        ->select('COUNT(l.id)')
        ->where('l.dateLike >= :date')
        ->setParameter('date', $date)
        ->getQuery()
        ->getSingleScalarResult();
    }
    public function countLikesDerniersJours(int $jours = 7): int
    {
    $date = new \DateTimeImmutable("-{$jours} days");

    return $this->createQueryBuilder('l')
        ->select('COUNT(l.id)')
        ->where('l.dateLike >= :date')
        ->setParameter('date', $date)
        ->getQuery()
        ->getSingleScalarResult();
    }



//    /**
//     * @return Like[] Returns an array of Like objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Like
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
