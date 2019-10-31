<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Theme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Theme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Theme[]    findAll()
 * @method Theme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    // /**
    //  * @return Theme[] Returns an array of Theme objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Theme
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function OrderByNameASC()
    {

        $query = $this->createQueryBuilder('t')
                      ->orderBy('t.name', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function OrderByNameDESC()
    {

        $query = $this->createQueryBuilder('t')
                      ->orderBy('t.name', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function OrderByDateASC()
    {

        $query = $this->createQueryBuilder('t')
                      ->orderBy('t.created_at', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function OrderByDateDESC()
    {

        $query = $this->createQueryBuilder('t')
                      ->orderBy('t.created_at', 'DESC');

        return $query->getQuery()->getResult();
    }




    public function findByName($name)
    {
        $query = $this->createQueryBuilder('t')
                      ->where('t.name LIKE :searchName')
                      ->setParameter('searchName',  $name )
                      ->setMaxResults(1);
 

        return $query->getQuery()->getResult();
    }
}
