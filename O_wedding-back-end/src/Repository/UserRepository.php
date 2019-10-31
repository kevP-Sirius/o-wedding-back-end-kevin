<?php

namespace App\Repository;

use DateTime;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function OrderByUsernameASC()
    {

        $query = $this->createQueryBuilder('u')
                      ->orderBy('u.username', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function OrderByUsernameDESC()
    {

        $query = $this->createQueryBuilder('u')
                      ->orderBy('u.username', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function OrderByDateASC()
    {

        $query = $this->createQueryBuilder('u')
                      ->orderBy('u.created_at', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function OrderByDateDESC()
    {

        $query = $this->createQueryBuilder('u')
                      ->orderBy('u.created_at', 'DESC');

        return $query->getQuery()->getResult();
    }

    

    public function findByNameUser($username)
    {
        $query = $this->createQueryBuilder('u')
                      ->where('u.username LIKE :searchUsername')
                      ->setParameter('searchUsername', $username)
                      ->setMaxResults(1);
 

        return $query->getQuery()->getResult();
    }

    public function findByMail($email)
    {
        $query = $this->createQueryBuilder('u')
                      ->where('u.email LIKE :searchEmail')
                      ->setParameter('searchEmail', $email)
                      ->setMaxResults(1);
 

        return $query->getQuery()->getResult();
    }

    public function findByJwt($jwt)
    {
        $query = $this->createQueryBuilder('u')
                      ->where('u.token LIKE :searchToken')
                      ->setParameter('searchToken', $jwt)
                      ->setMaxResults(1);
 

        return $query->getQuery()->getResult();
    }

    public function findConnectedNumber($status,$CurrentTimestamp)
    {

       
        $query = $this->createQueryBuilder('u')
                      ->where('u.is_connect LIKE :searchStatus')
                      ->andWhere('u.session_duration > :searchDuration')
                      ->setParameters(array(
                          'searchStatus'=> $status,
                          'searchDuration'=> $CurrentTimestamp
                      ));
                      
 

        return $query->getQuery()->getResult();
    }

}
