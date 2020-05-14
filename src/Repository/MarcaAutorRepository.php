<?php

namespace App\Repository;

use App\Entity\MarcaAutor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MarcaAutor|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarcaAutor|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarcaAutor[]    findAll()
 * @method MarcaAutor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarcaAutorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarcaAutor::class);
    }

    public function findAllByMy($userId){
    return $this->createQueryBuilder('m')
        ->andWhere('m.propietario = :user')
        ->setParameter('user', $userId)
        ->getQuery()
        ->getResult()
        ;
    }

    public function findMySigns($userId){
        return $this->createQueryBuilder('m')
            ->andWhere('m.propietario = :user')
            ->orWhere('m.id = 1')
            ->setParameter('user', $userId)
            ;
    }


    // /**
    //  * @return MarcaAutor[] Returns an array of MarcaAutor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MarcaAutor
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
