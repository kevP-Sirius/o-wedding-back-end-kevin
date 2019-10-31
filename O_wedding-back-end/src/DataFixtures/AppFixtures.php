<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use Firebase\JWT\JWT;
use App\Entity\Department;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\GuestRepository;
use App\Repository\ThemeRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProviderRepository;
use App\Repository\DepartmentRepository;
use App\DataFixtures\MyCustomNativeLoader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    private $encoder;
   
    
    public function __construct(UserPasswordEncoderInterface $encoder,UserRepository $UR ,RoleRepository $RR,GuestRepository $GR,DepartmentRepository $DR,ProjectRepository $PR,ProviderRepository $PV,ThemeRepository $TH)
    {
        
      $this->encoder = $encoder;   
      $this->UR = $UR;
      $this->RR = $RR;
      $this->GR = $GR;
      $this->DR = $DR;
      $this->PR = $PR;
      $this->PV = $PV;
      $this->TH = $TH;

    }
    public function load(ObjectManager $em)
    {
        $loader = new MyCustomNativeLoader();
        
        //importe le fichier de fixtures et récupère les entités générés
        $entities = $loader->loadFile(__DIR__.'/fixtures.yml')->getObjects();
        
        
        foreach ($entities as $entity) {
            

            $em->persist($entity);
            

        }
        
        //enregistre
        $em->flush();

        
      
        $users = $this->UR->findall();
        $roles = $this->RR->findall();
        $guests = $this->GR->findall();
        $providers = $this->PV->findall();
        $faker =  Factory::create('fr_FR'); 

    
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        foreach($guests as $guest)
        {
            $key = "alfa1";
            $token = array(
            
            "firstname" =>  $guest->getFirstname(),
            "lastname" => $guest->getLastname(),
            "created_at" =>$CurrentTimestamp,
            );

            $jwt = JWT::encode($token, $key);
           
           
            $guest->setToken($jwt);
            
            $em->persist($guest);
            $em->flush();
        }
        foreach ($users as $user ) {
         
            $key = "alfa1";
            $token = array(
            "id" => $user->getId(),
            "username" => $user->getUsername()
            );
            $jwt = JWT::encode($token, $key);
            $user->setToken($jwt);

            $encodedPassword = $this->encoder->encodePassword($user, $user->getPassword()); 
            $user->setUsername($faker->unique()->username);
            $user->setPassword($encodedPassword);
            $em->persist($user);
            $em->flush();

        }

        

        foreach($roles as $role){
            if($role->getName() == 'user'){
                $role->setRoleString('ROLE_USER');
            }
            if($role->getName() == 'administrator'){
                $role->setRoleString('ROLE_ADMIN');
            }
            $em->persist($role);
            $em->flush();
        }

        $roleString = 'ROLE_ADMIN' ;
        $adminRole = $this->RR->findByRoleString($roleString);
        $userAdmin =new User ;
        $userAdmin->setUsername('admin');
        $userAdmin->setRole($adminRole[0]);
        $userAdmin->setEmail('oweddingproject@gmail.com');
        $encodedPassword = $this->encoder->encodePassword($userAdmin, 'Prime$972!'); 
        $userAdmin->setPassword($encodedPassword);
        $em->persist($userAdmin);
        $em->flush();

        //theme traiteur
      
        $pictureTakeway = [];
        $pictureTakeway = [
        
            'https://cdn.pixabay.com/photo/2017/08/30/17/25/restaurant-2697945_960_720.jpg',
            'https://cdn.pixabay.com/photo/2019/04/19/17/48/cake-balls-4139982_960_720.jpg',
            'https://cdn.pixabay.com/photo/2014/11/24/23/32/cake-544725_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/02/17/09/45/wedding-cake-639181_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/11/15/02/08/cupcakes-1825136_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/09/30/04/01/wedding-cake-1704427_960_720.jpg',
            'https://cdn.pixabay.com/photo/2018/04/02/15/10/food-3284093_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/11/10/12/43/cake-1814225_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/11/06/14/44/a-variety-of-cakes-1803074_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/10/25/16/03/wedding-gifts-2888414_960_720.jpg',
            'https://cdn.pixabay.com/photo/2018/07/05/13/27/cake-3518314_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/07/26/21/02/wedding-cake-1543831_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/02/22/13/09/cake-644963_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/03/16/21/14/sweet-dessert-1261776_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/05/11/21/42/wedding-1386553_960_720.jpg',


        ];

     
        $price=null;
        $department=null;
        $theme = 'Traiteur';
        $providerTraiteur = $this->PV->findByPTDCriteria($price,$theme,$department);
        
        foreach($providerTraiteur as $provider){
            
            shuffle($pictureTakeway);

            $provider->setDescription('Vos convives se rappellerons de votre mariage après avoir gouté à nos plats');
            $provider->setPicture($pictureTakeway[0]);
            $em->persist($provider);
            $em->flush();
            
        }


        // théme DJ

        $pictureDJ = [];
        $pictureDJ = [
        
            'https://cdn.pixabay.com/photo/2016/11/22/19/15/dark-1850120_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/08/04/20/10/dj-2581269_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/04/11/22/53/lightshow-2223124_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/12/17/07/32/hip-hop-1912921_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/04/13/13/37/dj-720589_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/08/04/20/10/dj-2581269_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/03/30/12/37/pioneer-698515_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/09/06/20/52/disco-2722995_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/02/11/21/54/concert-633110_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/07/28/22/15/dj-865173_960_720.jpg',
            'https://images.pexels.com/photos/2111016/pexels-photo-2111016.jpeg',
            'https://images.pexels.com/photos/860707/pexels-photo-860707.jpeg',
            'https://images.pexels.com/photos/529930/pexels-photo-529930.jpeg',
            'https://images.pexels.com/photos/236095/pexels-photo-236095.jpeg',
            'https://images.pexels.com/photos/1694908/pexels-photo-1694908.jpeg',
        ];

     
        $price=null;
        $department=null;
        $theme = 'Musique';
        $providerDJ = $this->PV->findByPTDCriteria($price,$theme,$department);
        
        foreach($providerDJ as $provider){
            
            shuffle($pictureDJ);

            $provider->setDescription('Le meilleur DJ de l\'année');
            $provider->setPicture($pictureDJ[0]);
            $em->persist($provider);
            $em->flush();
            
        }

        // théme Animation

        $pictureAnimation = [];
        $pictureAnimation = [
        
            'https://cdn.pixabay.com/photo/2017/01/04/21/00/new-years-eve-1953253_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/07/25/05/48/hands-859302_960_720.jpg',
            'https://cdn.pixabay.com/photo/2018/07/10/13/02/card-3528638_960_720.jpg',
            'https://cdn.pixabay.com/photo/2013/04/02/19/54/playground-99509_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/02/02/23/36/magic-2034144_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/06/10/14/06/fireworks-804838_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/02/24/08/31/girl-1219339_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/12/27/19/19/acrobats-1934622_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/12/27/19/12/acrobats-1934548_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/11/29/06/17/audience-1867754_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/08/01/14/51/concert-2566002_960_720.jpg',

        ];

     
        $price=null;
        $department=null;
        $theme = 'Animation';
        $providerAnimation = $this->PV->findByPTDCriteria($price,$theme,$department);
        
        foreach($providerAnimation as $provider){
            
            shuffle($pictureAnimation);

            $provider->setDescription('Vous recherchez des animations pour votre mariage ? Nous vous proposons plusieurs services à moindre coût');
            $provider->setPicture($pictureAnimation[0]);
            $em->persist($provider);
            $em->flush();
            
        }

        // theme tailleurs
        $pictureDress = [];
        $pictureDress = [
            
            'https://cdn.pixabay.com/photo/2016/06/29/04/17/wedding-dresses-1485984_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/08/27/05/47/beautiful-909553_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/07/23/03/58/groom-1536233_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/08/05/14/51/wedding-2584186_960_720.jpg',
            'https://cdn.pixabay.com/photo/2014/03/31/10/09/bride-301813_960_720.jpg',
            'https://cdn.pixabay.com/photo/2014/03/31/10/10/wedding-dress-301817_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/05/04/10/44/baby-752188_960_720.jpg',
        ];

     
        $price=null;
        $department=null;
        $theme = 'Tailleurs';
        $providerDress = $this->PV->findByPTDCriteria($price,$theme,$department);
        
        foreach($providerDress as $provider){
            
            shuffle($pictureDress);

            $provider->setDescription('Robe de mariée, costume de marié et robe de cérémonie, notre boutique vous offre un large choix de tenue');
            $provider->setPicture($pictureDress[0]);
            $em->persist($provider);
            $em->flush();
            
        }

        // théme alliance
        $pictureRing = [];
        $pictureRing = [
        
            'https://cdn.pixabay.com/photo/2014/02/07/11/31/flowers-260894_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/02/06/04/13/love-2042101_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/03/25/12/38/wedding-688924_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/02/20/15/33/cake-2082939_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/07/27/08/45/wedding-2544405_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/06/16/03/13/ring-2407552_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/04/22/21/51/wedding-rings-2252438_960_720.jpg',
            'https://cdn.pixabay.com/photo/2014/10/22/04/11/love-497528_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/03/27/19/30/couple-1283859_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/06/17/20/00/wedding-812967_960_720.jpg',
        ];

     
        $price=null;
        $department=null;
        $theme = 'Alliances';
        $providerRing = $this->PV->findByPTDCriteria($price,$theme,$department);
        
        foreach($providerRing as $provider){
            
            shuffle($pictureRing);

            $provider->setDescription('Quelle soit en or blanc, jaune ou rose, nous avons l\'alliance qu\'il vous faut');
            $provider->setPicture($pictureRing[0]);
            $em->persist($provider);
            $em->flush();
            
        }

        //théme transport
        $pictureCar = [];
        $pictureCar = [
        
            'https://cdn.pixabay.com/photo/2015/05/08/11/19/wedding-758001_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/04/20/21/25/car-2247033_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/03/30/20/15/wedding-2189629_960_720.jpg',
            'https://cdn.pixabay.com/photo/2018/05/25/07/30/wedding-3428470_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/03/05/12/22/blue-2118528_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/09/30/20/59/coach-966055_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/05/23/20/08/mustang-2338377_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/01/19/15/19/wedding-1149219_960_720.jpg',
        ];

     
        $price=null;
        $department=null;
        $theme = 'Transport';
        $providerCar = $this->PV->findByPTDCriteria($price,$theme,$department);
        
        foreach($providerCar as $provider){
            
            shuffle($pictureCar);

            $provider->setDescription('Vous revez d\'une limousine pour le plus beau jour de votre vie, alors contactez-nous, nous avons tout ce qu\'il vous faut');
            $provider->setPicture($pictureCar[0]);
            $em->persist($provider);
            $em->flush();
            
        }

        //Théme Salle
        $pictureRoom = [];
        $pictureRoom = [
        
            'https://cdn.pixabay.com/photo/2016/11/23/17/56/bouquet-1854074_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/11/23/17/56/decor-1854075_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/11/09/21/28/exclusive-banquet-1812772_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/11/23/17/55/atoll-1854069_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/07/05/19/59/christening-1499314_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/08/08/00/17/events-2609526_960_720.jpg',
            'https://cdn.pixabay.com/photo/2018/05/15/14/20/house-3403116_960_720.jpg',
            'https://cdn.pixabay.com/photo/2013/09/25/17/09/reserved-186321_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/02/01/18/26/wedding-table-1174131_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/02/01/18/27/wedding-table-1174142_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/10/28/20/40/wedding-1011429_960_720.jpg',
            'https://cdn.pixabay.com/photo/2018/05/04/20/46/table-3374968_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/02/27/20/32/wedding-hall-1226238_960_720.jpg',
            'https://cdn.pixabay.com/photo/2014/09/20/13/55/banquet-453799_960_720.jpg',
            'https://cdn.pixabay.com/photo/2013/09/05/10/38/restaurant-bern-179047_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/03/05/19/32/affair-1238428_960_720.jpg',
            'https://cdn.pixabay.com/photo/2017/11/13/13/28/banquet-2945619_960_720.jpg',

    
        ];

     
        $price=null;
        $department=null;
        $theme = 'Salle';
        $providerRoom = $this->PV->findByPTDCriteria($price,$theme,$department);
        
        foreach($providerRoom as $provider){
            
            shuffle($pictureRoom);

            $provider->setDescription('Nous vous proposons un site merveilleux pour le plus beau jour de votre vie');
            $provider->setPicture($pictureRoom[0]);
            $em->persist($provider);
            $em->flush();
            
        }

       
        foreach($providers as $provider)
        {
            $provider->setName($faker->unique()->company);
            $em->persist($provider);
            $em->flush();
        }
        foreach($guests as $guest)
        {
            $guest->setFirstname($faker->unique()->firstName);
            $guest->setLastname($faker->unique()->LastName);
            $em->persist($guest);
            $em->flush();
        }
    }
}
