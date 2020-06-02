<?php

namespace App\Repository;

use App\Entity\WondArt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WondArt|null find($id, $lockMode = null, $lockVersion = null)
 * @method WondArt|null findOneBy(array $criteria, array $orderBy = null)
 * @method WondArt[]    findAll()
 * @method WondArt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WondArtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WondArt::class);
    }

    public function findAllByMy($userId){
        return $this->createQueryBuilder('w')
            ->join('w.marcaAutor', 'm')
            ->andWhere('m.propietario = :user')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findPageRangeWithSearch($from, $limit, $search){
        $search_query = $this->createQueryBuilder('w')
            ->andWhere('w.titulo like :search or w.etiquetas like :search')
            ->andWhere('w.publicado = 1')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('w.id', 'ASC')
            ->setFirstResult($from)
            ->setMaxResults($limit)
            ;

        return $search_query->getQuery()->getResult();

    }

    public function findPageRange($from, $limit){
        return $this->createQueryBuilder('w')
            ->andWhere('w.publicado = 1')
            ->orderBy('w.id', 'ASC')
            ->setFirstResult($from)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return WondArt[] Returns an array of WondArt objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WondArt
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
