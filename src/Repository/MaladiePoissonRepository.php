<?php

namespace App\Repository;

use App\Entity\MaladiePoisson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MaladiePoisson>
 */
class MaladiePoissonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaladiePoisson::class);
    }

/**
 * @return MaladiePoisson[]
 */
public function searchByNom(?string $nom): array
{
    $qb = $this->createQueryBuilder('m')
        ->orderBy('m.nom', 'ASC');

    if ($nom) {
        $qb
            ->andWhere('LOWER(m.nom) LIKE :q OR LOWER(m.agentPathogene) LIKE :q')
            ->setParameter('q', '%' . mb_strtolower($nom) . '%');
    }

    return $qb->getQuery()->getResult();
}


//    /**
//     * @return MaladiePoisson[] Returns an array of MaladiePoisson objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MaladiePoisson
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
