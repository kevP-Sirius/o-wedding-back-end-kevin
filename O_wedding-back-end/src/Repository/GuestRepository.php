<?php

namespace App\Repository;

use App\Entity\Guest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Guest|null find($id, $lockMode = null, $lockVersion = null)
 * @method Guest|null findOneBy(array $criteria, array $orderBy = null)
 * @method Guest[]    findAll()
 * @method Guest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guest::class);
    }

    // /**
    //  * @return Guest[] Returns an array of Guest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Guest
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findById($Id)
    {
        $query = $this->createQueryBuilder('g')
                      ->where('g.id LIKE :searchId')
                      ->setParameter('searchId', $Id )
                      ->setMaxResults(1);
 

        return $query->getQuery()->getResult();
    }

    public function checkNoMoreThan2Maried($Id1,$type)
    {
        $query = $this->createQueryBuilder('g')

                      ->innerjoin("g.type","t")
                      ->innerjoin("g.projects","p")
                      ->where('p.id LIKE :searchId')
                      ->setParameter('searchId',  $Id1 )
                      ->andWhere('t.name LIKE :searchName')
                      ->setParameter("searchName", $type)
                      ->setMaxResults(50);

        return $query->getQuery()->getResult();
    }

    public function checkIsComingGuest($Id1,$status)
    {
        $query = $this->createQueryBuilder('g')

                    ->innerjoin("g.projects","p")                  
                    ->where(':subProjectId MEMBER OF g.projects')
                    ->setParameter("subProjectId", $Id1)
                    ->andWhere('g.is_coming  = :searchStatus')
                    ->setParameter("searchStatus", $status)
                    ->setMaxResults(50);

        return $query->getQuery()->getResult();
    }

    public function findByDoubleId($Id1,$Id2)
    {
        $query = $this->createQueryBuilder('g')

                      ->innerjoin("g.projects","p")
                      ->where('g.id LIKE :searchId')
                      ->setParameter('searchId',  $Id1 )
                      ->andWhere(':subProjectId MEMBER OF g.projects')
                      ->setParameter("subProjectId", $Id2)
                      ->setMaxResults(1);

        return $query->getQuery()->getResult();
    }

   

    public function findByJwt($jwt)
    {
        $query = $this->createQueryBuilder('g')
                      ->where('g.token LIKE :searchToken')
                      ->setParameter('searchToken', $jwt )
                      ->setMaxResults(1);
 

        return $query->getQuery()->getResult();
    }
}
