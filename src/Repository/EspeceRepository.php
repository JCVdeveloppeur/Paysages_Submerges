<?php
// Existant
namespace App\Repository;

use App\Entity\Espece;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Espece>
 */
class EspeceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Espece::class);
    }
    public function findByBiotope(?string $biotopeLabel): array
    {
    $qb = $this->createQueryBuilder('e')
        ->orderBy('e.nomCommun', 'ASC'); // ou nomScientifique

    if ($biotopeLabel) {
        $qb->andWhere('e.biotope = :bio')
           ->setParameter('bio', $biotopeLabel);
    }

    return $qb->getQuery()->getResult();
    }
    public function findIdsForExplorer(?string $biotopeSlug, ?string $search): array
{
    $qb = $this->createQueryBuilder('e')
        ->select('e.id')
        ->orderBy('e.nomCommun', 'ASC');

    // biotope en DB = label (ex: "Asie du Sud-Est")
    $biotopeMap = [
        'amerique-sud'      => 'Amérique du sud',
        'amerique-centrale' => 'Amérique centrale',
        'asiatique'         => 'Asie du Sud-Est',
        'africain'          => 'Afrique',
        'australien'        => 'Australie',
        'europeen'          => 'Europe',
        'eaux-saumatres'    => 'Eaux saumâtres',
        'mangroves'         => 'Mangroves',
        'autre'             => 'Autre',
    ];

    if ($biotopeSlug && isset($biotopeMap[$biotopeSlug])) {
        $qb->andWhere('e.biotope = :bio')
           ->setParameter('bio', $biotopeMap[$biotopeSlug]);
    }

    if ($search) {
        $q = '%' . mb_strtolower($search) . '%';
        $qb->andWhere('LOWER(e.nomCommun) LIKE :q OR LOWER(e.nomScientifique) LIKE :q OR LOWER(e.biotope) LIKE :q')
           ->setParameter('q', $q);
    }

    return array_map('intval', array_column($qb->getQuery()->getArrayResult(), 'id'));
}


//    /**
//     * @return Espece[] Returns an array of Espece objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Espece
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
