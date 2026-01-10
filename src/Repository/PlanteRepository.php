<?php

namespace App\Repository;

use App\Entity\Plante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plante>
 */
class PlanteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plante::class);
    }
    
    public function findIdsForExplorer(?string $biotope, ?string $nom): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.id')
            ->orderBy('p.nomCommun', 'ASC');

        if ($biotope) {
            $qb->andWhere('p.biotope = :bio')->setParameter('bio', $biotope);
        }

        if ($nom) {
            $qb->andWhere('p.nomCommun LIKE :q OR p.nomScientifique LIKE :q')
            ->setParameter('q', '%' . $nom . '%');
        }

        // retourne un tableau d'ids [1, 5, 8, ...]
        return array_map('intval', array_column($qb->getQuery()->getArrayResult(), 'id'));
    }


//    /**
//     * @return Plante[] Returns an array of Plante objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Plante
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
