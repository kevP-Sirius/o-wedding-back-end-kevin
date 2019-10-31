<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    // /**
    //  * @return Project[] Returns an array of Project objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function OrderByNameASC()
    {

        $query = $this->createQueryBuilder('p')
                      ->orderBy('p.name', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function OrderByNameDESC()
    {

        $query = $this->createQueryBuilder('p')
                      ->orderBy('p.name', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function OrderByDateASC()
    {

        $query = $this->createQueryBuilder('p')
                      ->orderBy('p.created_at', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function OrderByDateDESC()
    {

        $query = $this->createQueryBuilder('p')
                      ->orderBy('p.created_at', 'DESC');

        return $query->getQuery()->getResult();
    }

    
    public function findByDepartment($number){
        $query = $this->createQueryBuilder('p')
                    
        ->innerjoin("p.department","d")
        ->where('d.number LIKE :number')
        ->setParameters (array(

            'number' =>$number,
           
        ));
        
    }

    public function findByUsername($username){
        $query = $this->createQueryBuilder('p')
                    
        ->innerjoin("p.user","u")
        ->where('u.username LIKE :username')
        ->setParameters (array(

            'username' =>$username,
           
        ))
        ->setMaxResults(1);
    }

    public function findByEmail($email){
        $query = $this->createQueryBuilder('p')
                    
        ->innerjoin("p.user","u")
        ->where('u.email LIKE :email')
        ->setParameters (array(

            'email' =>$email,
           
        ))
        ->setMaxResults(1);
    }

    public function findByJwt($jwt)
    {
        $query = $this->createQueryBuilder('p')
                      ->where('p.token LIKE :searchToken')
                      ->setParameter('searchToken',  $jwt )
                      ->setMaxResults(1);
 

        return $query->getQuery()->getResult();
    }

    public function findById($Id)
    {
        $query = $this->createQueryBuilder('p')
                      ->where('p.id LIKE :searchId')
                      ->setParameter('searchId',  $Id )
                      ->setMaxResults(1);
 

        return $query->getQuery()->getResult();
    }

    public function checkNoMoreThan2Maried($type)
    {
        $query = $this->createQueryBuilder('p')
        ->innerjoin("p.guest","g")
        ->innerjoin("g.type","t")
        ->where('t.name LIKE :searchName')
        ->setParameter("searchName", $type)
        ->setMaxResults(50);

        return $query->getQuery()->getResult();
    }

    public function findGuestProject($id)
    {
        $query = $this->createQueryBuilder('p')
        ->innerjoin("p.guest","g")
        ->innerjoin("g.type","t")
        ->where('g.id LIKE :searchId')
        ->setParameter("searchId", $id);
       

        return $query->getQuery()->getResult();
    }
}
