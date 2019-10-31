<?php

namespace App\Repository;

use App\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Department|null find($id, $lockMode = null, $lockVersion = null)
 * @method Department|null findOneBy(array $criteria, array $orderBy = null)
 * @method Department[]    findAll()
 * @method Department[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    // /**
    //  * @return Department[] Returns an array of Department objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Department
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function OrderByNameASC()
    {

        $query = $this->createQueryBuilder('d')
                      ->orderBy('d.name', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function OrderByNameDESC()
    {

        $query = $this->createQueryBuilder('d')
                      ->orderBy('d.name', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function OrderBynumberASC()
    {

        $query = $this->createQueryBuilder('d')
                      ->orderBy('d.number', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function OrderByNumberDESC()
    {

        $query = $this->createQueryBuilder('d')
                      ->orderBy('d.number', 'DESC');

        return $query->getQuery()->getResult();
    }



    public function findByNumber($number)
    {
        $query = $this->createQueryBuilder('d')
                      ->where('d.number LIKE :searchNumber')
                      ->setParameter('searchNumber', $number)
                      ->setMaxResults(1);
 

        return $query->getQuery()->getResult();
    }
}
