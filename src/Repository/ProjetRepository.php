<?php

namespace App\Repository;

use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Projet>
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projet::class);
    }

    public function findAccessibleProjects($user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.archive = false')
            ->orderBy('p.id', 'DESC');

        // Si pas admin on ne garde que les projets où l'utilisateur est assigné
        if (!in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $qb
                ->innerJoin('p.employes', 'e')
                ->andWhere('e = :user')
                ->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }


    //    /**
    //     * @return Projet[] Returns an array of Projet objects
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

    //    public function findOneBySomeField($value): ?Projet
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
