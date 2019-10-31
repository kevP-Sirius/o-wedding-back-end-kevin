<?php

namespace App\Repository;

use App\Entity\Provider;
use App\Repository\ThemeRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Provider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Provider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Provider[]    findAll()
 * @method Provider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
        
    }

    // /**
    //  * @return Provider[] Returns an array of Provider objects
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
    public function findOneBySomeField($value): ?Provider
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


    public function findById($Id)
    {
        $query = $this->createQueryBuilder('g')
                      ->where('g.id LIKE :searchId')
                      ->setParameter('searchId',  $Id )
                      ->setMaxResults(1);
 

        return $query->getQuery()->getResult();
    }
    
    public function findByDoubleId($Id1,$Id2)
    {
        $query = $this->createQueryBuilder('p')
                      ->where('p.id LIKE :searchId')
                      ->setParameter('searchId',  $Id1 )
                      ->andWhere(':subProjectId MEMBER OF p.projects')
                      ->setParameter("subProjectId", $Id2)
                      ->setMaxResults(1);

        return $query->getQuery()->getResult();
    }

    /**
    * @return Provider[] Returns an array of Provider objects
    */
    public function findByPTDCriteria($price,$theme,$department)
    {   
       
        if($price!=null && $theme!=null && $department!=null)
        {
            $query = $this->createQueryBuilder('p')
                    
                    ->innerjoin("p.theme","t")
                    ->innerjoin("p.department","d")
                    ->where('p.average_price < :searchPrice')
                    ->andWhere('t.name LIKE :name')
                    ->andWhere('d.number = :department')
                    ->setParameters (array(

                        'searchPrice' => $price,
                        'name' =>$theme,
                        'department'=>$department
                    ))
                    ->setMaxResults(150);

                return $query->getQuery()->getResult();
        }   
            
        if($price!=null && $theme==null && $department!=null)
        {
            $query = $this->createQueryBuilder('p')
                    
                    
                    ->innerjoin("p.department","d")
                    ->where('p.average_price < :searchPrice')
                    ->andWhere('d.number = :department')
                    ->setParameters (array(

                        'searchPrice' => $price,
                        'department'=>$department
                    ))
                    ->setMaxResults(150);

                return $query->getQuery()->getResult();
        }   

        if($price!=null && $theme !=null && $department==null)
        {
            $query = $this->createQueryBuilder('p')
                
            ->innerjoin("p.theme","t")
            ->where('p.average_price < :searchPrice')
            ->andWhere('t.name LIKE :name')
            ->setParameters (array(

                'searchPrice' => $price,
                'name' =>$theme,
            ))
            ->setMaxResults(150);

            return $query->getQuery()->getResult();
        }

        if($price!=null && $theme ==null && $department==null)
        {
            $query = $this->createQueryBuilder('p')
                
            
            ->where('p.average_price < :searchPrice')
            ->setParameters (array(
                'searchPrice' => $price,
                
            ))
            ->setMaxResults(150);

            return $query->getQuery()->getResult();
        }

        if($price==null && $theme==null && $department!=null)
        {
            $query = $this->createQueryBuilder('p')
                    
                    
                    ->innerjoin("p.department","d")
                   
                    
                    ->where('d.number = :department')
                    ->setParameters (array(

                        
                        'department'=>$department
                    ))
                    ->setMaxResults(150);

                return $query->getQuery()->getResult();
        } 
        
        if($price==null && $theme!=null && $department==null)
        {
            $query = $this->createQueryBuilder('p')
                    
                    ->innerjoin("p.theme","t")
                    ->where('t.name = :name')
                    ->setParameters (array(
                        'name' =>$theme,      
                    ))
                    ->setMaxResults(150);

                return $query->getQuery()->getResult();
        }
        
        if($price==null && $theme!=null && $department!=null)
        {
            $query = $this->createQueryBuilder('p')
                    
                    ->innerjoin("p.theme","t")
                    ->innerjoin("p.department","d")
                    ->where('t.name LIKE :name')
                    ->andWhere('d.number = :department')
                    ->setParameters (array(
                        'name' =>$theme,
                        'department'=>$department
                    ))
                    ->setMaxResults(150);

                return $query->getQuery()->getResult();
        }   
        
       

            

       
    }
  

    

}
