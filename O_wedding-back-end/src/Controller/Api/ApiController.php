<?php

namespace App\Controller\Api;

use DateTime;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Guest;
use App\Form\ApiType;
use Firebase\JWT\JWT;
use App\Form\ApiType2;
use App\Entity\Project;
use App\Form\GuestType;
use Symfony\Component\Mercure\Update;
use App\Repository\RoleRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use App\Repository\GuestRepository;
use Symfony\Component\Mailer\Mailer;
use App\Repository\ProjectRepository;
use App\Repository\ProviderRepository;
use App\Repository\DepartmentRepository;
use Symfony\Component\Mercure\Publisher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class ApiController extends AbstractController
{

    /**
     * @Route("/api/signin", name="signin",  methods={"GET","HEAD", "POST" })
     */
    public function signin(\Swift_Mailer $mailer ,UserRepository $UserRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em,ProjectRepository $ProjectRepository)
    {
       
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        { 

            
            
            $date = new DateTime();
            $CurrentTimestamp = $date->getTimestamp();
            $usernameOrEmail = $request->request->get('username');
            $password = $request->request->get('password');
            

            if($usernameOrEmail !=null && $password!=null)
            {

                $currentUserConnect = $UserRepository->findByNameUser($usernameOrEmail);
                $currentUserConnectEmail = $UserRepository->findByMail($usernameOrEmail);
                
                if($currentUserConnect==[] && $currentUserConnectEmail==[])
                {
    
    
    
                    $LoginStatus='failure the user does not exist';
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [   
                            'loginstatus' => $LoginStatus,
                        ]));
    
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
                if($currentUserConnect!=[] && $currentUserConnect[0]->getIsActive()===false)
                {
                    $LoginStatus='failure the account has been banned';
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [   
                            'loginstatus' => $LoginStatus,
                        ]));
    
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }

                if($currentUserConnectEmail!=[] && $currentUserConnectEmail[0]->getIsActive()===false)
                {
                    $LoginStatus='failure the account has been banned';
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [   
                            'loginstatus' => $LoginStatus,
                        ]));
    
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }

    
               if($currentUserConnect!=[] && $currentUserConnect[0 ]->getIsConnect()===false )
                {
                    
                    $plainPassword = $password;
                    $verify = $encoder->isPasswordValid($currentUserConnect[0], $plainPassword, $currentUserConnect[0]->getSalt());
                    // si le password est valide et que le user existe on passe à la suite
                    if($verify === true )
                    {
                        //je crée ici le timestamp dont je vais me servir pour crée une $session_duration 
                        $date = new DateTime();
                        $timestamp = $date->getTimestamp();
                        $session_duration=$timestamp+3600;
                        $currentUserConnect[0]->setSessionDuration($session_duration);
                        $em->persist($currentUserConnect[0]);
                        $em->flush();
                        //je crée ici le token avec le passphrase
                        $key = "alfa1";
                        $token = array(

                            "id" => $currentUserConnect[0]->getId(),
                            "username" => $currentUserConnect[0]->getUsername(),
                            "session_duration"=>$session_duration
                        
                        );
    
                        $jwt = JWT::encode($token, $key);
                        
                       
                        
                        $currentUserConnect[0]->setToken($jwt);
                        $currentUserConnect[0]->setIsConnect(true);
                        if($currentUserConnect[0]->getProject()!=null)
                        {
                            $currentUserConnect[0]->getProject()->setToken($jwt);
                            $em->persist($currentUserConnect[0]);
                            $em->flush();
                            $projectStatus = true ;
                            $connectedByStatus = 'username' ;
                            $loginStatus='succes';
                            $sessionStatus = 'start';
                            $response = new Response();
                            $response->setContent(json_encode(
                                
                            [  
                                
                                'project_status' => $projectStatus,
                                'connected_by' =>  $connectedByStatus ,
                                'token' => $jwt ,
                                'session_status'=> $sessionStatus,
                                'login_status' => $loginStatus,
                                'username' => $currentUserConnect[0]->getUsername(),
                                
                            ]));
                            return $response ;
                        }else{

                            $em->persist($currentUserConnect[0]);
                            $em->flush();
                            $projectStatus = false ;
                            $connectedByStatus = 'username' ;
                            $loginStatus='succes';
                            $sessionStatus = 'start';
                            $response = new Response();
                            $response->setContent(json_encode(
        
                            [  
                                
                                'project_status' => $projectStatus,
                                'connected_by' =>  $connectedByStatus ,
                                'token' => $jwt ,
                                'session_status'=> $sessionStatus,
                                'login_status' => $loginStatus,
                                'username' => $currentUserConnect[0]->getUsername(),
                                
                            ]));
                            return $response ;
                        }
                        
                       
                       
    
    
                    }else{
                        
                        
                        $errorStatus = 'wrong password';
                        $loginStatus='failure';
                        $sessionStatus = 'refused';
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
                                
        
                                
                                'error' =>$errorStatus,
                                'session_status'=> $sessionStatus,
                                'login_status' => $loginStatus,
                                'username' => $currentUserConnect[0]->getUsername(),
                                
                            ]));
                        return $response ;
    
    
                    }

                } 
                
                if($currentUserConnect!=[] && $currentUserConnect[0]->getIsConnect()===true )
                {   
                   

                    if($currentUserConnect[0]->getIsAlertActive()===true && $currentUserConnect[0]->getSessionDuration()> $CurrentTimestamp)
                    {
                        $currentDate = new \DateTime;
                        $message = (new \Swift_Message('O\'wedding - securité'))
                        ->setFrom('O\'wedding@project.fr')
                        ->setTo($currentUserConnect[0]->getEmail())
                        ->setBody(
                            $this->renderView(
                            
                                'api/email/alertIntruder.html.twig',
                                ['name' => $currentUserConnect[0]->getUsername(),
                                'date'=>$currentDate]
                            ),
                            'text/html'
                        ) ;
                        $result = $mailer->send($message);
                        $token = $currentUserConnect[0]->getToken();
                        $CurrentProject = $ProjectRepository->findByJwt($token);
                        if($CurrentProject!=[])
                        {
                            $CurrentProject[0]->setToken('');
                        }
                        $currentUserConnect[0]->setToken('');
                        $currentUserConnect[0]->setIsConnect(false);
                        $em->persist($currentUserConnect[0]);
                        $em->flush();
                        $errorStatus = 'already connected , please reconnect';
                        $loginStatus='failure';
                        $sessionStatus = 'refused';
                        $response = new Response();


                        $response->setContent(json_encode(
        
                            [  
                                
        
                                
                                'error' =>$errorStatus,
                                'session_status'=> $sessionStatus,
                                'login_status' => $loginStatus,
                                'username' => $currentUserConnect[0]->getUsername(),
                                
                            ]));
                        return $response ;

                    }
                    

                    if($currentUserConnect!=[] && $currentUserConnect[0]->getSessionDuration()<$CurrentTimestamp)
                    {
                        $plainPassword = $password;
                        $verify = $encoder->isPasswordValid($currentUserConnect[0], $plainPassword, $currentUserConnect[0]->getSalt());
                       
                        if($verify === true )
                        {
                            
                            $date = new DateTime();
                            $timestamp = $date->getTimestamp();
                            $session_duration=$timestamp+3600;
                            $currentUserConnect[0]->setSessionDuration($session_duration);
                            $em->persist($currentUserConnect[0]);
                            $em->flush();
                            $key = "alfa1";
                            $token = array(

                                "id" => $currentUserConnect[0]->getId(),
                                "username" => $currentUserConnect[0]->getUsername(),
                                "session_duration"=>$session_duration
                            
                            );
        
                            $jwt = JWT::encode($token, $key);
                            
                           
                            
                            $currentUserConnect[0]->setToken($jwt);
                            $currentUserConnect[0]->setIsConnect(true);
                            if($currentUserConnect[0]->getProject()!=null)
                            {
                                $currentUserConnect[0]->getProject()->setToken($jwt);
                                $em->persist($currentUserConnect[0]);
                                $em->flush();
                                $projectStatus = true ;
                                $connectedByStatus = 'username' ;
                                $loginStatus='succes';
                                $sessionStatus = 'start';
                                $response = new Response();
                                $response->setContent(json_encode(
                                    
                                [  
                                    
                                    'project_status' => $projectStatus,
                                    'connected_by' =>  $connectedByStatus ,
                                    'token' => $jwt ,
                                    'session_status'=> $sessionStatus,
                                    'login_status' => $loginStatus,
                                    'username' => $currentUserConnect[0]->getUsername(),
                                    
                                ]));
                                return $response ;
                            }else{
    
                                $em->persist($currentUserConnect[0]);
                                $em->flush();
                                $projectStatus = false ;
                                $connectedByStatus = 'username' ;
                                $loginStatus='succes';
                                $sessionStatus = 'start';
                                $response = new Response();
                                $response->setContent(json_encode(
            
                                [  
                                    
                                    'project_status' => $projectStatus,
                                    'connected_by' =>  $connectedByStatus ,
                                    'token' => $jwt ,
                                    'session_status'=> $sessionStatus,
                                    'login_status' => $loginStatus,
                                    'username' => $currentUserConnect[0]->getUsername(),
                                    
                                ]));
                                return $response ;
                            }
                            
                           
                           
        
        
                        }else{
                            
                            
                            $errorStatus = 'wrong password';
                            $loginStatus='failure';
                            $sessionStatus = 'refused';
                            $response = new Response();
                            $response->setContent(json_encode(
            
                                [  
                                    
            
                                    
                                    'error' =>$errorStatus,
                                    'session_status'=> $sessionStatus,
                                    'login_status' => $loginStatus,
                                    'username' => $currentUserConnect[0]->getUsername(),
                                    
                                ]));
                            return $response ;
        
        
                        }
                    }

                }
    
    
                if($currentUserConnectEmail!=[]  && $currentUserConnectEmail[0]->getIsConnect()===false)
                {
                    $plainPassword = $password;
                    $verify = $encoder->isPasswordValid($currentUserConnectEmail[0], $plainPassword, $currentUserConnectEmail[0]->getSalt());
                    
                    if($verify === true )
                    {
                       
                        $date = new DateTime();
                        $timestamp = $date->getTimestamp();
                        $session_duration=$timestamp+3600;
                        $currentUserConnectEmail[0]->setSessionDuration($session_duration);
                       
                        $key = "alfa1";
                        $token = array(
                        "id" => $currentUserConnectEmail[0]->getId(),
                        "username" => $currentUserConnectEmail[0]->getUsername()
                        );
    
                        $jwt = JWT::encode($token, $key);
                        
                        $currentUserConnectEmail[0]->setToken($jwt);
                        $currentUserConnectEmail[0]->setIsConnect(true);
                        $em->persist($currentUserConnectEmail[0]);
                        $em->flush();
                        if($currentUserConnectEmail[0]->getProject()!=null)
                        {
                            $currentUserConnectEmail[0]->getProject()->setToken($jwt);
                            $em->persist($currentUserConnectEmail[0]);
                            $em->flush();
                            $projectStatus = true ;
                            $connectedByStatus = 'email' ;
                            $loginStatus='succes';
                            $sessionStatus = 'start';
                            $response = new Response();
                            $response->setContent(json_encode(
                                
                            [  
                                
                                'project_status' => $projectStatus,
                                'connected_by' =>  $connectedByStatus ,
                                'token' => $jwt ,
                                'session_status'=> $sessionStatus,
                                'login_status' => $loginStatus,
                                'username' => $currentUserConnectEmail[0]->getUsername(),
                                
                            ]));
                            return $response ;
                        }else{
                            
                            $em->persist($currentUserConnectEmail[0]);
                            $em->flush();
                            $projectStatus = false ;
                            $connectedByStatus = 'email' ;
                            $loginStatus='succes';
                            $sessionStatus = 'start';
                            $response = new Response();
                            $response->setContent(json_encode(
        
                            [  
                                
                                'project_status' => $projectStatus,
                                'connected_by' =>  $connectedByStatus ,
                                'token' => $jwt ,
                                'session_status'=> $sessionStatus,
                                'login_status' => $loginStatus,
                                'username' => $currentUserConnectEmail[0]->getUsername(),
                                
                            ]));
                            return $response ;
                        }
                       


                       
    
    
                    }else{
    
                     
                        $errorStatus = 'wrong password';
                        $loginStatus='failure';
                        $sessionStatus = 'refused';
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
                                
        
                                
                                'error' =>$errorStatus,
                                'session_status'=> $sessionStatus,
                                'login_status' => $loginStatus,
                                'username' => $currentUserConnect[0]->getUsername(),
                                
                            ]));
                    return $response ;
    
    
                    }

                }
                if($currentUserConnectEmail!=[] && $currentUserConnectEmail[0]->getIsConnect()===true)
                {
                   
                   if($currentUserConnectEmail[0]->getIsAlertActive()===true  && $currentUserConnectEmail[0]->getSessionDuration()> $CurrentTimestamp)
                    {
                        $currentDate = new \DateTime;
                        $message = (new \Swift_Message('O\'wedding - securité'))
                        ->setFrom('O\'wedding@project.fr')
                        ->setTo($currentUserConnectEmail[0]->getEmail())
                        ->setBody(
                            $this->renderView(
                            
                                'api/email/alertIntruder.html.twig',
                                ['name' => $currentUserConnectEmail[0]->getUsername(),
                                'date'=>$currentDate]
                            ),
                            'text/html'
                        ) ;
                        $result = $mailer->send($message);

                        $currentUserConnectEmail[0]->setToken('');
                        $currentUserConnectEmail[0]->setIsConnect(false);
                        $em->persist($currentUserConnectEmail[0]);
                        $em->flush();
                        $errorStatus = 'already connected , please reconnect';
                        $loginStatus='failure';
                        $sessionStatus = 'refused';
                        $response = new Response();
    
                        $response->setContent(json_encode(
        
                            [  
                                
        
                                
                                'error' =>$errorStatus,
                                'session_status'=> $sessionStatus,
                                'login_status' => $loginStatus,
                                'username' => $currentUserConnectEmail[0]->getUsername(),
                                
                            ]));
                        return $response ;
                    }

                    if($currentUserConnectEmail!=[] && $CurrentUserConnectEmail[0]->getSessionDuration()<$CurrentTimestamp)
                    {
                        $plainPassword = $password;
                        $verify = $encoder->isPasswordValid($currentUserConnectEmail[0], $plainPassword, $currentUserConnectEmail[0]->getSalt());
                       
                        if($verify === true )
                        {
                            
                            $date = new DateTime();
                            $timestamp = $date->getTimestamp();
                            $session_duration=$timestamp+3600;
                            $currentUserConnectEmail[0]->setSessionDuration($session_duration);
                            $em->persist($currentUserConnectEmail[0]);
                            $em->flush();
                            $key = "alfa1";
                            $token = array(

                                "id" => $currentUserConnectEmail[0]->getId(),
                                "username" => $currentUserConnectEmail[0]->getUsername(),
                                "session_duration"=>$session_duration
                            
                            );
        
                            $jwt = JWT::encode($token, $key);
                            
                           
                            
                            $currentUserConnectEmail[0]->setToken($jwt);
                            $currentUserConnectEmail[0]->setIsConnect(true);
                            if($currentUserConnectEmail[0]->getProject()!=null)
                            {
                                $currentUserConnectEmail[0]->getProject()->setToken($jwt);
                                $em->persist($currentUserConnectEmail[0]);
                                $em->flush();
                                $projectStatus = true ;
                                $connectedByStatus = 'username' ;
                                $loginStatus='succes';
                                $sessionStatus = 'start';
                                $response = new Response();
                                $response->setContent(json_encode(
                                    
                                [  
                                    
                                    'project_status' => $projectStatus,
                                    'connected_by' =>  $connectedByStatus ,
                                    'token' => $jwt ,
                                    'session_status'=> $sessionStatus,
                                    'login_status' => $loginStatus,
                                    'username' => $currentUserConnectEmail[0]->getUsername(),
                                    
                                ]));
                                return $response ;
                            }else{
    
                                $em->persist($currentUserConnectEmail[0]);
                                $em->flush();
                                $projectStatus = false ;
                                $connectedByStatus = 'username' ;
                                $loginStatus='succes';
                                $sessionStatus = 'start';
                                $response = new Response();
                                $response->setContent(json_encode(
            
                                [  
                                    
                                    'project_status' => $projectStatus,
                                    'connected_by' =>  $connectedByStatus ,
                                    'token' => $jwt ,
                                    'session_status'=> $sessionStatus,
                                    'login_status' => $loginStatus,
                                    'username' => $currentUserConnectEmail[0]->getUsername(),
                                    
                                ]));
                                return $response ;
                            }
                            
                           
                           
        
        
                        }else{
                            
                            
                            $errorStatus = 'wrong password';
                            $loginStatus='failure';
                            $sessionStatus = 'refused';
                            $response = new Response();
                            $response->setContent(json_encode(
            
                                [  
                                    
            
                                    
                                    'error' =>$errorStatus,
                                    'session_status'=> $sessionStatus,
                                    'login_status' => $loginStatus,
                                    'username' => $currentUserConnectEmail[0]->getUsername(),
                                    
                                ]));
                            return $response ;
        
        
                        }
                    }
                    
                   
                  

                }

               

               



            }else{

                $loadingDataStatus = 'failure';
                $errorStatus ='missing data';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'loading_data_status'=> $loadingDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;

            }
   
            


                


        }else{

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
           
                    
            return $response ;
        }
        
    }

     /**
     * @Route("/api/alert/change-status", name="alert_status",  methods={"GET","HEAD", "POST" })
     */

    public function alertStatus(\Swift_Mailer $mailer ,UserRepository $UserRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em,ProjectRepository $ProjectRepository)
    {
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {
            $username = $request->request->get('username');
            $token = $request->request->get('token');
            
            if($username!=null && $token!= null)
            {

                $date = new DateTime();
                $CurrentTimestamp = $date->getTimestamp();

                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $ProjectRepository->findByJwt($token);

                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }

                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
                {  
                    
                    
                    
                    
                    if($CurrentUserConnect[0]->getIsAlertActive()===true)
                    {
                        $CurrentUserConnect[0]->setIsAlertActive(false);
                        $AlertStatus='desactivated';

                    }else{

                        if($CurrentUserConnect[0]->getIsAlertActive()===false)
                        {
                            $CurrentUserConnect[0]->setIsAlertActive(true);
                            $AlertStatus='activated';
                        }
                    }
                   
                    $em->persist($CurrentUserConnect[0]);
                    $em->flush();
                    $changeAlertStatus = 'success';
                    $errorStatus = '';
                    $response = new Response();
                    $response->headers->set('Content-Type', 'application/json');
                    $response->setContent(json_encode(
                
                        [  
                
                            'alert_status' => $AlertStatus,
                            'change_alert_status' =>  $changeAlertStatus,
                               
                        ]));
                       
                                
                        return $response ;
                }





            }else{

                $logoutStatus = 'failure';
                $errorStatus = 'missing data';
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent(json_encode(
        
                    [  
        
                        
                        'logout_status'=> $logoutStatus ,
                        'error_status'=> $errorStatus 
                       
                    ]));
               
                        
                return $response ;
            }


        }else{

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
           
                    
            return $response ;
        }
    }


     /**
     * @Route("/api/logout" , name="logout" )
     */

    public function logout( \Swift_Mailer $mailer ,EntityManagerInterface $em ,UserPasswordEncoderInterface $encoder, ?Request $request=null, RoleRepository $role,UserRepository $UserRepository,ProjectRepository $projectRepository)
    { 


        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        { 

            $username = $request->request->get('username');
            $token = $request->request->get('token');
            
            if($username!=null && $token!= null)
            {
                
                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                

                if($CurrentUserConnect==[] )
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }

              
               

              

                if($CurrentUserConnect!=[] && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }


                if($CurrentUserConnect!=[]  &&  $CurrentUserConnect[0]->getUsername()== $username )
                {  
                    $date = new DateTime();
                    $CurrentTimestamp = $date->getTimestamp();
                    $session_duration= $CurrentTimestamp;
                    $CurrentUserConnect[0]->setSessionDuration($session_duration);
                    $CurrentUserConnect[0]->setToken('');
                    $CurrentUserConnect[0]->setIsConnect(false);
                    if($CurrentUserConnect[0]->getProject()!=null)
                    {
                        $CurrentUserConnect[0]->getProject()->setToken('');
                    }
                    $em->persist($CurrentUserConnect[0]);
                    $em->flush();
                    $logoutStatus = 'success';
                    $errorStatus = '';
                    $response = new Response();
                    $response->headers->set('Content-Type', 'application/json');
                    $response->setContent(json_encode(
                
                        [  
                
                                
                            'logout_status' => $logoutStatus,
                               
                        ]));
                       
                                
                        return $response ;
                }

            }else{

                $logoutStatus = 'failure';
                $errorStatus = 'missing data';
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent(json_encode(
        
                    [  
        
                        
                        'logout_status'=> $logoutStatus ,
                        'error_status'=> $errorStatus 
                       
                    ]));
               
                        
                return $response ;
            }

        }else{

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
        
                    
            return $response ;
        }


    }
    

    /**
     * @Route("/api/signup" , name="signup" , methods={"GET","HEAD", "POST" })
     */

    public function signup( \Swift_Mailer $mailer ,EntityManagerInterface $em ,UserPasswordEncoderInterface $encoder, ?Request $request=null, RoleRepository $role,UserRepository $UserRepository)
    {       


       

        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';

        if($request!= null && $request->headers->get('JWT') == $API_Token )
        {

            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $email =  $request->request->get('email');

            if($email !=null && $username !=null && $password!=null)
            {



                $roleString = 'ROLE_USER' ;
                $userRole = $role->findByRoleString($roleString);
                $currentUserConnectName = $UserRepository->findByNameUser($username);
                $currentUserConnectEmail = $UserRepository->findByMail($email);
                
                if($currentUserConnectName==[] && $currentUserConnectEmail==[])
                {
                   
    
                   
                   
                    $user = new User;
                    $user->setUsername($username);
                    $user->setEmail($email);
                    $plainPassword = $password;
                    $encoded = $encoder->encodePassword($user, $plainPassword);
                    $user->setPassword($encoded);
                    $user->setRole($userRole[0]);
                    $em->persist($user);
                    $em->flush();
                    $message = (new \Swift_Message('Bienvenue sur O\'wedding'))
                    ->setFrom('O\'wedding@project.fr')
                    ->setTo($email)
                    ->setBody(
                        $this->renderView(
                            // templates/emails/registration.html.twig
                            'api/email/registration.html.twig',
                            ['name' => $username]
                        ),
                        'text/html'
                    ) ;
                    
                    $result = $mailer->send($message);
                    $subscribeStatus='done';
                    $response = new Response();
                    $response->setContent(json_encode(

                                [    
                                   
                                    'mailStatus' => 'send',
                                    'subscribe_Status' => $subscribeStatus,
                                ]));
                    $response->headers->set('Content-Type', 'application/json');
            
                           
                    
                        
                    return $response ;
                }else{
                    
                    if($currentUserConnectName!=[] && $currentUserConnectEmail!=[])
                    {
                        $subscribeStatus='fail';
                        $errorStatus = 'username and email already in use';
                        $response = new Response();
                        $response->setContent(json_encode(
                            [
                                
                                'subscribeStatus' => $subscribeStatus,
                                'error' => $errorStatus
                            ]));

                            return $response ;


                    }else{

                        if($currentUserConnectName!=[])
                        {
                            $subscribeStatus='fail';
                            $errorStatus = 'username already in use';
                            $response = new Response();
                            $response->setContent(json_encode(
                                [
                                    
                                    'subscribeStatus' => $subscribeStatus,
                                    'error' => $errorStatus
                                ]));
        
                                return $response ;
        
                        }
                        if($currentUserConnectEmail!=[])
                        {
                            $subscribeStatus='fail';
                            $errorStatus = 'email already in use';
                            $response = new Response();
                            $response->setContent(json_encode(
                                [
                                    
                                    'subscribeStatus' => $subscribeStatus,
                                    'error' => $errorStatus
                                ]));
        
                                return $response ;
                        }

                    }
                

                

                }

            }else{

                $loadingDataStatus = 'failure';
                $errorStatus ='missing data';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'loading_data_status'=> $loadingDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }

        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
    
         }



    }



     /**
     * @Route("/api/reset_password" , name="reset" , methods={"GET","HEAD", "POST" })
     */

    public function resetPasswordRequest( \Swift_Mailer $mailer ,EntityManagerInterface $em ,UserPasswordEncoderInterface $encoder, ?Request $request=null, RoleRepository $role,UserRepository $UserRepository)
    {      

        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
        
        if($request!= null && $request->headers->get('JWT') == $API_Token )
        {

            $email =  $request->request->get('email');
            $currentUserConnectEmail = $UserRepository->findByMail($email);
            if($currentUserConnectEmail==[])
            {
                $changePasswordStatus = 'failed' ;
                $errorStatus = 'unknown email' ;
                $response = new Response();
                $response->setContent(json_encode(

                    [  

                        'change_password_status' => $changePasswordStatus,
                        'error_status' => $errorStatus ,
                    
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }

            if($currentUserConnectEmail!=[])
            {   

                $key = "alfa1";
                $token = array(
                "id" => $currentUserConnectEmail[0]->getId(),
                "username" => $currentUserConnectEmail[0]->getUsername()
                );  

                $jwt = JWT::encode($token, $key);

                $currentUserConnectEmail[0]->setToken($jwt);
                $em->persist($currentUserConnectEmail[0]);
                $em->flush();
                $email = $currentUserConnectEmail[0]->getEmail();
                $resetPasswordStatus = 'accepted' ;
                $errorStatus = '' ;
                $emailStatus ='sended';
                $response = new Response();
                $response->setContent(json_encode(

                    [  
                        'reset_password_status'=> $resetPasswordStatus,
                        'email_status' => $emailStatus
                        

                    
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                
                

                $baseUrl ='http://www.owedding.fr/password/confirm_reset/user/';
                
              
                $message = (new \Swift_Message('Changement de votre mot de passe sur O\'wedding'))
                ->setFrom('O\'Wedding@project.com')
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        // templates/emails/registration.html.twig
                        'api/email/forgottenPassword.html.twig',
                        [   
                            'link' => $baseUrl.$jwt,
                            'name'=> $currentUserConnectEmail[0]->getUsername()

                        ]
                    ),
                    'text/html'
                );
                                

               
                $result = $mailer->send($message);
        
                
        
                        
                return $response ;
            }
        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }

    

    /**
     * @Route("/api/account/update_password" , name="update_password" )
     */

    public function updatePassword( \Swift_Mailer $mailer ,EntityManagerInterface $em ,UserPasswordEncoderInterface $encoder, ?Request $request=null, RoleRepository $role,UserRepository $UserRepository,ProjectRepository $projectRepository )
    {  

        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
        
        if($request!= null && $request->headers->get('JWT') == $API_Token )
        {   

            
            $username =  $request->request->get('username');
            $oldpassword =  $request->request->get('oldpassword');
            $newpassword =  $request->request->get('newpassword');
            $token =  $request->request->get('token');

            if($username != null && $oldpassword !=null && $newpassword !=null && $token!=null )
            {
                $CurrentUserConnect = $UserRepository->findByJwt($token);

                if($CurrentUserConnect!=[])
                { 
                    
                    $date = new DateTime();
                    $CurrentTimestamp = $date->getTimestamp();

                   

                    if($CurrentUserConnect!=[] && $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp && $CurrentUserConnect[0]->getUsername()== $username)
                    {
                        $plainPassword = $oldpassword;
                        $verify = $encoder->isPasswordValid($CurrentUserConnect[0], $plainPassword, $CurrentUserConnect[0]->getSalt());
                        
                        if($verify === true )
                        {
                            $plainPassword = $newpassword;
                            $encoded = $encoder->encodePassword($CurrentUserConnect[0], $plainPassword);
                            $CurrentUserConnect[0]->setPassword($encoded); 
                            $em->persist($CurrentUserConnect[0]);
                            $em->flush();
                            
        
                            $updateStatus ='success';
                            $response = new Response();
                            $response->setContent(json_encode(
            
                                [  
                                    
                                    'update_password_status' => $updateStatus,
                                    'username' => $CurrentUserConnect[0]->getUsername(),
                                    
                                ]));
                            

                            $email = $CurrentUserConnect[0]->getEmail();
                            
                            
                            $message = (new \Swift_Message('Mise à jours de votre mot de passe sur O\'wedding'))
                            ->setFrom('O\'wedding@project.com')
                            ->setTo($email)
                            ->setBody(
                                $this->renderView(
                                    
                                    'api/email/updatePasswordConfirmationEmail.html.twig',
                                    [   
                                        
                                        'name'=> $CurrentUserConnect[0]->getUsername()

                                    ]
                                ),
                                'text/html'
                            );

                            $result = $mailer->send($message);

                            return $response ;

        
                            
                        }else{
        
                         
                            $errorStatus = 'wrong old password';
                            $updatePasswordStatus='failure';
                          
                            $response = new Response();
                            $response->setContent(json_encode(
            
                                [  
                                    
            
                                    'update_password_status' =>$updatePasswordStatus,
                                    'error' =>$errorStatus,
                                    'username' => $CurrentUserConnect[0]->getUsername(),
                                    
                                ]));
                            return $response ;
        
        
                        }
                    
                    
                    
                    }

                    if($CurrentUserConnect!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp && $CurrentUserConnect[0]->getUsername()== $username)
                    {
                        $loadDataStatus = 'failed';
                        $errorStatus = 'Session expired please reconnect' ;
                        $response = new Response();
                        $response->setContent(json_encode(
            
                            [  
            
                                'load_data_status' => $loadDataStatus,
                                'error_status' => $errorStatus ,
                                    
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                    
                        return $response ;
            
                    }

                    if($CurrentUserConnect!=[] &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername() != $username )
                    {   
            
                        $updateDataStatus = 'failed';
                        $errorStatus = 'The username given does not match with the token' ;
                        $response = new Response();
                        $response->setContent(json_encode(
            
                            [  
                                    
                                'update_data_status' => $updateDataStatus,
                                'error_status' => $errorStatus ,
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                    
                        return $response ;
                           
                    }
                        
                 
                }else{

                    if($CurrentUserConnect==[])
                    {  
                        $updatePasswordStatus = 'failure';
                        $errorStatus = 'no user found';
                        $response = new Response();
                        $response->setContent(json_encode(
            
                            [  
            
                                'update_password_status'=>$updatePasswordStatus ,
                                'error_status' =>  $errorStatus
            
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    }
                }

               


            }else{

                $updatePasswordStatus = 'failure';
                $errorStatus = 'missing data';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [        
                        'update_password_status'=>$updatePasswordStatus ,
                        'error_status' =>  $errorStatus         
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;

            }

        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }



     /**
     * @Route("/confirm_reset/user/{jwt}" , name="confirm_reset" )
     */

    public function confirm_reset( \Swift_Mailer $mailer ,EntityManagerInterface $em ,UserPasswordEncoderInterface $encoder, ?Request $request=null, RoleRepository $role,UserRepository $UserRepository, $jwt)
    {   
       
       

        $userOnChangePW = $UserRepository->findByJwt($jwt);
       

        if($userOnChangePW !==[])
        {    
                  
            $form = $this->createForm(ApiType::class, $userOnChangePW[0]);
               
                
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                  
                   
                $encodedPassword = $encoder->encodePassword($userOnChangePW[0], $userOnChangePW[0]->getPassword());

                
                $userOnChangePW[0]->setPassword($encodedPassword);
        
                $userOnChangePW[0]->setToken('');
                $em->persist($userOnChangePW[0]);
                $em->flush();

                return $this->render('api/changePassword/processSuccessPassword.html.twig');
                    
                   
        
                  
            }
        
            return $this->render('api/changePassword/processForm.html.twig',[
                'form' => $form->createView()
            ]);

                
        }else{

            return $this->render('api/changePassword/processFailPassword.html.twig');
        }

        
           
    }    

     /**
     * @Route("/api/project/new", name="project_new",  methods={"GET","HEAD", "POST" })
     */
    public function newProject(DepartmentRepository $departmentRepository,UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em)
    {           
               
                $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        { 

           
            $date = new DateTime();
            $CurrentTimestamp = $date->getTimestamp();
            $token = $request->request->get('token');
            $username = $request->request->get('username');
            $name = $request->request->get('name');
            $date = $request->request->get('date');
            $forecast_budget = $request->request->get('forecast_budget');
            $department = $request->request->get('department');
            $CurrentUserConnect = $UserRepository->findByJwt($token);
            // token/id/username/name/date/forecast_budget/departement|
            if($token!=null && $username != null)
            {
               

                if($name!=null && $date!= null && $forecast_budget!= null && $department!=null )
                {
                    if($CurrentUserConnect==[])
                    {
        
                        
                        $loadDataStatus = 'failed';
                        $errorStatus = 'no user found' ;
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
        
                                'load_data_status' => $loadDataStatus,
                                'error_status' => $errorStatus ,
                            
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
        
                    
                    }
        
                    if($CurrentUserConnect!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp && $CurrentUserConnect[0]->getUsername()== $username)
        
                    {
                        $loadDataStatus = 'failed';
                        $errorStatus = 'Session expired please reconnect' ;
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
        
                                'load_data_status' => $loadDataStatus,
                                'error_status' => $errorStatus ,
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
        
                    }
        
                    if($CurrentUserConnect!=[] &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                    {   
        
                        $updateDataStatus = 'failed';
                        $errorStatus = 'The username given does not match with the token' ;
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
                                
                                'update_data_status' => $updateDataStatus,
                                'error_status' => $errorStatus ,
                            
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    
                    }
        
                    if($CurrentUserConnect!=[] && $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp &&$CurrentUserConnect[0]->getUsername()== $username)
        
                    {   
                        $CurrentProject = $projectRepository->findByJwt($token);
        
                        if($CurrentProject ==[])
                        {
        
                        
                            $departmentAsked = $departmentRepository->findByNumber($department);
                            if($departmentAsked!=[])
                            {
                                

                               
                                $NewProject = new Project ;
                                $NewProject->setName($name);
                                $NewProject->setDeadline($date);
                                $NewProject->setForecastBudget($forecast_budget);
                                $NewProject->setCurrentBudget(0);
                                $NewProject->setDepartment($departmentAsked[0]);
                                $NewProject->setToken($token);
                                $CurrentUserConnect[0]->setProject($NewProject);
                                
                                $em->persist($CurrentUserConnect[0]);
                                $em->flush();
                                
                                $errorStatus = '' ;
                                $createProjectStatus = 'success';
                                $response = new Response();
                                $response->setContent(json_encode(
            
                                    [  
                                        'create_project_status' => $createProjectStatus,
                                        
                                    
                                        
                                    
                                    ]));
                                $response->headers->set('Content-Type', 'application/json');
                                        
                                return $response ;

                            }else{

                                $errorStatus = 'the department asked does not exist' ;
                                $createProjectStatus = 'failure';
                                $response = new Response();
                                $response->setContent(json_encode(
            
                                    [  
                                        'create_project_status' => $createProjectStatus,
                                        'errer_status' => $errorStatus,
                                        
                                    
                                    ]));
                                $response->headers->set('Content-Type', 'application/json');
                                        
                                return $response ;
                        
                            }

                        }

                        if($CurrentProject!=[])
                        {
                            
                            $errorStatus = 'the user already have a project' ;
                            $createProjectStatus = 'failure';
                            $response = new Response();
                            $response->setContent(json_encode(
        
                                [  
                                    'create_project_status' => $createProjectStatus,
                                    'errer_status' => $errorStatus,
                                    
                                
                                ]));
                            $response->headers->set('Content-Type', 'application/json');
                                    
                            return $response ;
                       
                        }

                    }
    
                   
    
                }else{

                    $loadingDataStatus = 'failure';
                    $errorStatus ='missing data(date/name/forecast_budget/department)';
                    $response = new Response();
                    $response->setContent(json_encode(
        
                        [  
                            
                            'loading_data_status'=> $loadingDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


            }else{

                $loadingDataStatus = 'failure';
                $errorStatus ='missing data(token/username';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'loading_data_status'=> $loadingDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }
               


        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }


     /**
     * @Route("/api/project/contents/test", name="show_project_test",  methods={"GET","HEAD", "POST" })
     */

    public function showProjectTest(ProviderRepository $ProviderRepository,UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder)
    {
        $id = '1' ;
        $CurrentProject = $projectRepository->findById($id);
        $AllProvider = $ProviderRepository->findAll();
        $loadDataStatus = 'success';
                    
        $providerProjectData = [];
        foreach ($CurrentProject[0]->getProvider() as $providerProject) {
            
            foreach($providerProject->getDepartment() as $departmentProviderProject ){
                $providerDepartmentNumberProject = $departmentProviderProject->getNumber();
                $providerDepartmentNameProject = $departmentProviderProject->getName();
              
            }

              foreach($providerProject->getTheme() as $ThemeProviderProject){
                  $providerThemeNameProject = $ThemeProviderProject->getName();
              }
            $providerProjectData[] =
            [
            
                'id' => $providerProject->getId(),
                'name' => $providerProject->getName(),
                
                'email' => $providerProject->getEmail(),
                'phone_number'=>$providerProject->getPhoneNumber(),
                'average_price' =>$providerProject->getAveragePrice(),
                'provider_department_number' =>  $providerDepartmentNumberProject,
                'provider_department_name' => $providerDepartmentNameProject ,
                'provider_theme' => $providerThemeNameProject,
                'provider_description' =>$providerProject->getDescription(),
                'provider_image' =>$providerProject->getPicture(),

            ];

          
           
        }

        $guestProjectData=[];
        foreach($CurrentProject[0]->getGuest() as $guestProject ){
           
            $guestProjectData[] = 
            [
                'firstname'=> $guestProject->getFirstname(),
                'lastname' => $guestProject->getLastname(),
                'email' => $guestProject->getEmail(),
                'phone_number' => $guestProject->getPhoneNumber()
            ];

        }

        $providerList=[];
        foreach($AllProvider as $provider){

            foreach($provider->getDepartment() as $departmentProvider ){
               $departmentProvider->getNumber();
               $departmentProvider->getName();
            }

            foreach($provider->getTheme() as $providerTheme){
                $providerTheme->getName();
            }
            $providerList[]=
            [
                'id' => $provider->getId(),
                'name' => $provider->getName(),
                'email' => $provider->getEmail(),
                'phone_number'=>$provider->getPhoneNumber(),
                'average_price' =>$provider->getAveragePrice(),
                'provider_department_number' =>  $departmentProvider->getNumber(),
                'provider_department_name' => $departmentProvider->getName(),
                'provider_theme' =>  $providerTheme->getName(),
                'provider_description' =>$provider->getDescription(),
                'provider_image' =>$provider->getPicture(),

            ];

            
            
        }

        $errorStatus = '' ;
        $response = new Response();
        $response->setContent(json_encode(

            [   
               
                'project_data' =>
                [
                    'id' => $CurrentProject[0]->getId() ,
                    'name'=>$CurrentProject[0]->getName() ,
                    'deadline'=>$CurrentProject[0]->getDeadline(),
                    'forecast_budget' => $CurrentProject[0]->getForecastBudget(),
                    'current_budget' => $CurrentProject[0]->getForecastBudget() - $CurrentProject[0]->getCurrentBudget(),
                    'user'=>$CurrentProject[0]->getUser()->getUsername(),
                    'department_number' =>$CurrentProject[0]->getDepartment()->getNumber(),
                    'department_name'=> $CurrentProject[0]->getDepartment()->getName(),
                    'provider_project_list' => $providerProjectData ,
                    'Guest_list' => $guestProjectData,
                    
                ],
                'provider_list'=> $providerList,
                'load_data_status' => $loadDataStatus,
                
            
            ]));
        $response->headers->set('Content-Type', 'application/json');
                
        return $response ;


                   
               

    }

     /**
     * @Route("/api/project/show", name="show_project",  methods={"GET","HEAD", "POST" })
     */

    public function showProject(ProviderRepository $ProviderRepository ,GuestRepository $GuestRepository, UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder)
    {
       
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        { 
           
               
            $date = new DateTime();
            $CurrentTimestamp = $date->getTimestamp();
            
            $token = $request->request->get('token');

            $username = $request->request->get('username');
            
            if($token !=null && $username !=null)
            {

                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);
                $AllProvider = $ProviderRepository->findAll();

                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown or missing JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }
                 if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown or missing JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }

                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $loadDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)
                {
                    $loadDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
                {   
    
                    $loadDataStatus = 'failed';
                    $errorStatus = ' A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }
    
                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
                {
                    $status = 1;
                    $guestListIsComing = $GuestRepository->checkIsComingGuest($CurrentProject[0]->getId(), $status);
                    $guestListSum = 0;
                    $VegetarianMealsSum=0;
                    $meatMealsSum=0;
                    if($guestListIsComing!=[])
                    {
                        $guestListNumber=[];
                        foreach ($guestListIsComing as $guestComing)
                        {
                            $guestListNumber[]=array_sum( [
                                'people'=>$guestComing->getIsComingWith(),
                            ]);
                           
                        }
                       
                        $guestListSum = array_sum($guestListNumber);



                        $VegetarianMeals=[];
                        foreach ($guestListIsComing as $guestVegetarianMeal)
                        {
                            $VegetarianMeals[]=array_sum( [
                                'people'=>$guestVegetarianMeal->getVegetarianMealNumber(),
                            ]);
                           
                        }
                       
                        $VegetarianMealsSum = array_sum($VegetarianMeals);


                        $meatMeals=[];
                        foreach ($guestListIsComing as $guestMeatMeal)
                        {
                            $meatMeals[]=array_sum( [
                                'people'=>$guestMeatMeal->getMeatMealNumber(),
                            ]);
                           
                        }
                       
                        $meatMealsSum = array_sum($meatMeals);

                       
                       

                    }
                    $totalMealsSum=$VegetarianMealsSum+$meatMealsSum;
                   
                    
                    $loadDataStatus = 'success';
                    
                    $providerProjectData = [];
                    foreach ($CurrentProject[0]->getProvider() as $providerProject)
                    {

                        foreach($providerProject->getDepartment() as $departmentProviderProject ){
                          $DepartmentNumberProject = $departmentProviderProject->getNumber();
                            $DepartmentNameProject = $departmentProviderProject->getName();
                        
                        }

                        foreach($providerProject->getTheme() as $ThemeProviderProject){
                            $themeNameProject = $ThemeProviderProject->getName();
                        }
                        $providerProjectData[] =
                        [
                        
                            'id' => $providerProject->getId(),
                            'name' => $providerProject->getName(),                           
                            'email' => $providerProject->getEmail(),
                            'phone_number'=>$providerProject->getPhoneNumber(),
                            'average_price' =>$providerProject->getAveragePrice(),
                            'provider_department_name' =>$DepartmentNameProject,
                            'provider_department_number' =>$DepartmentNumberProject,
                            'provider_theme_name' =>  $themeNameProject,
                            'provider_description' => $providerProject->getDescription(),
                            'provider_picture' => $providerProject->getPicture(),

                        ];

                       
                       
                    }

                    $guestProjectData=[];
                    $guestIsComingNumber;
                    foreach($CurrentProject[0]->getGuest() as $guestProject )
                    {   if($guestProject->getType()!=null)
                        {
                            $guestProjectData[] = 
                            [   'id' => $guestProject->getId(),
                                'firstname'=> $guestProject->getFirstname(),
                                'lastname' => $guestProject->getLastname(),
                                'email' => $guestProject->getEmail(),
                                'phone_number' => $guestProject->getPhoneNumber(),
                                'type' => $guestProject->getType()->getName(),
                                
    
                            ];
                        }

                        if($guestProject->getType()==null)
                        {
                            $guestProjectData[] = 
                            [   'id' => $guestProject->getId(),
                                'firstname'=> $guestProject->getFirstname(),
                                'lastname' => $guestProject->getLastname(),
                                'email' => $guestProject->getEmail(),
                                'phone_number' => $guestProject->getPhoneNumber(),
                                'type' => 'non remplis'
                                
    
                            ];
                        }

                       
                    }

                    $providerList=[];
                    foreach($AllProvider as $provider){
                       
                        foreach($provider->getDepartment() as $departmentProvider ){
                            $DepartmentNumber = $departmentProvider->getNumber();
                            $DepartmentName = $departmentProvider->getName();
                          
                        }
  
                          foreach($provider->getTheme() as $ThemeProvider){
                              $themeName = $ThemeProvider->getName();
                          }

                        $providerList[]=
                        [

                            'id' => $provider->getId(),
                            'name' => $provider->getName(),
                            
                            'email' => $provider->getEmail(),
                            'phone_number'=>$provider->getPhoneNumber(),
                            'average_price' =>$provider->getAveragePrice(),
                            'provider_department_name' =>$DepartmentName,
                            'provider_department_number' =>$DepartmentNumber,
                            'provider_theme_name' =>  $themeName ,
                            'provider_description' => $provider->getDescription(),
                            'provider_picture' => $provider->getPicture(),


                        ];

                      
                        
                    }

                    
                    $errorStatus = '' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [   
                           
                            'project_data' =>
                            [   'id' => $CurrentProject[0]->getId() ,
                                'name'=>$CurrentProject[0]->getName() ,
                                'deadline'=>$CurrentProject[0]->getDeadline(),
                                'forecast_budget' => $CurrentProject[0]->getForecastBudget(),
                                'current_budget' => $CurrentProject[0]->getForecastBudget() - $CurrentProject[0]->getCurrentBudget(),
                                'total people' => $guestListSum,
                                'total_vegetarian_meal' => $VegetarianMealsSum,
                                'total_meat_meal' =>$meatMealsSum,
                                'total_meals' => $totalMealsSum,
                                'department_number' =>$CurrentProject[0]->getDepartment()->getNumber(),
                                'department_name'=> $CurrentProject[0]->getDepartment()->getName(),
                                'provider_project_list' => $providerProjectData ,
                                'Guest_list' => $guestProjectData,
                                
                                
                            ],
                            'load_data_status' => $loadDataStatus,
                            
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }
            
            }else{
                $loadingDataStatus = 'failure';
                $errorStatus ='missing data';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'loading_data_status'=> $loadingDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }


            
    





        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }


     /**
     * @Route("/api/project/edit/name", name="update_name",  methods={"GET","HEAD", "POST" })
     */

    public function updateName(UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {   
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();

        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {
            
            $newName = $request->request->get('newName');

            $token = $request->request->get('token');

            $username = $request->request->get('username');

            if($token!=null && $username!=null && $newName!=null)
            {   


                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);
           

                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown or missing JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }


                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
    
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
    
                {

                    $CurrentProject[0]->setName($newName);
                    $em->persist($CurrentProject[0]);
                    $em->flush();

                    $updateDataStatus = 'success';
                    $errorStatus = '';
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }
                    





            }else{

                $loadingDataStatus = 'failure';
                $errorStatus ='missing data(username/token/newName)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'loading_data_status'=> $loadingDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }



            
        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
        

    }


     /**
     * @Route("/api/project/edit/date", name="update_date",  methods={"GET","HEAD", "POST" })
     */

    public function updateDate(UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {


            $newDate = $request->request->get('newDate');

            $token = $request->request->get('token');

            $username = $request->request->get('username');



            if($token!=null && $username!=null && $newDate!=null)
            {

                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);

                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown or missing JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }


                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
    
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }
                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
                {

                    $CurrentProject[0]->setDeadline($newDate);
                    $em->persist($CurrentProject[0]);
                    $em->flush();

                    $updateDataStatus = 'success';
                    $errorStatus = '';
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }

            }else{

                $loadingDataStatus = 'failure';
                $errorStatus ='missing data(username/token/newDate)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'loading_data_status'=> $loadingDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }


            
        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }

     /**
     * @Route("/api/project/edit/budget", name="update_budget",  methods={"GET","HEAD", "POST" })
     */

    public function updateBudget(UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {

            $newBudget = $request->request->get('newBudget');

            $token = $request->request->get('token');

            $username = $request->request->get('username');

            if($token!=null && $username!=null && $newBudget!=null)
            {

                 $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);

                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown or missing JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }


                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
    
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
    
                {

                    $CurrentProject[0]->setForecastBudget($newBudget);
                    $em->persist($CurrentProject[0]);
                    $em->flush();

                    $updateDataStatus = 'success';
                    $errorStatus = '';
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }

            }else{

                $loadingDataStatus = 'failure';
                $errorStatus ='missing data(username/token/newBudget)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'loading_data_status'=> $loadingDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }


            
        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }

     /**
     * @Route("/api/project/guests/create", name="create_guests",  methods={"GET","HEAD", "POST" })
     */

    public function createGuests(TypeRepository $TypeRepository ,GuestRepository $GuestRepository, UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em)
    {   
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {

            $type = $request->request->get('type');
            $lastname = $request->request->get('lastname');
            $firstname = $request->request->get('firstname');
            $email = $request->request->get('email');
            $phoneNumber = $request->request->get('phone_number');
            $token = $request->request->get('token');
            

            $username = $request->request->get('username');

            if($token!=null && $username!=null)
            {   

                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);

                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown or missing JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }


                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
    
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
    
                {   
                    if($firstname !=null && $lastname != null)
                    {
                        $key = "alfa1";
                        $token = array(
                        "project_id" => $CurrentProject[0]->getId(),
                        "firstname" => $firstname,
                        "lastname" => $lastname,
                        "created_at" =>$CurrentTimestamp,
                        );
    
                        $jwt = JWT::encode($token, $key);
                        $guest = new Guest ;
                        $guest->setFirstname($firstname);
                        $guest->setLastname($lastname);
                        $guest->setToken($jwt);
                        if($email!=null){
                            $guest->setEmail($email);
                        }
                        if($phoneNumber != null){
                            $guest->setPhoneNumber($phoneNumber);
                        }
                        if($type==null)
                        {
                            $typeByDefault = $TypeRepository->findByName('guest');
                            $guest->setType($typeByDefault[0]);
                        }
                        if($type!=null)
                        {
                           $typeAsked= $TypeRepository->findByName($type);
                           
                           if($typeAsked[0]->getName()=='maried')
                           {
                               $guestMariedVerify = $GuestRepository->checkNoMoreThan2Maried($CurrentProject[0]->getId(),'maried');
                               
                               if(count($guestMariedVerify)>=2)
                               {
                                $loadingDataStatus = 'failure';
                                $errorStatus ='there is already 2 maried created';
                                $response = new Response();
                                $response->setContent(json_encode(
                    
                                    [  
                                        
                                        'loading_data_status'=> $loadingDataStatus,
                                        'error_status' => $errorStatus ,
                                        
                                    ]));
                                $response->headers->set('Content-Type', 'application/json');
                                        
                                return $response ;
                               }else{

                                $guest->setType($typeAsked[0]);

                               }
                           }
                           if($typeAsked[0]->getName()!='maried'){
                            $guest->setType($typeAsked[0]);
                           }
                            
                        }
                        $CurrentProject[0]->addGuest($guest);
                        $em->persist($CurrentProject[0]);
                        $em->flush();
                        
                        $CreateGuestStatus = 'success';
                        $errorStatus = '';
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
                                
                                'create_guest_status' => $CreateGuestStatus,
                                
                            
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    
                    
                    }else{

                        $loadingDataStatus = 'failure';
                        $errorStatus ='missing data (firstname/lastname)';
                        $response = new Response();
                        $response->setContent(json_encode(
            
                            [  
                                
                                'loading_data_status'=> $loadingDataStatus,
                                'error_status' => $errorStatus ,
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    }




                }

            }else{

                $loadingDataStatus = 'failure';
                $errorStatus ='missing data (token/username)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'loading_data_status'=> $loadingDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }


            
        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }



     /**
     * @Route("/api/project/guests/edit", name="update_guests",  methods={"GET","HEAD", "POST" })
     */

    public function updateGuests(GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {
            $type = $request->request->get('type');
            $guestId = $request->request->get('id');
            $lastname = $request->request->get('lastname');
            $firstname = $request->request->get('firstname');
            $email = $request->request->get('email');
            $phoneNumber = $request->request->get('phone_number');
            $token = $request->request->get('token');
            $isComing = $request->request->get('is_coming');
            $isActive = $request->request->get('is_active');
            $username = $request->request->get('username');

            if($token!=null && $username!=null)
            {   

                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);


                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown or missing JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }


                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
    
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
                {   
                    
                    $guestSelected = $GuestRepository->findbyDoubleId($guestId,$CurrentProject[0]->getId());

                    if($guestSelected!=[])
                    {   
                        if($isActive !=null){

                            $guestSelected[0]->setIsActive($isActive);
                        }
                        if($isComing !=null){

                            $guestSelected[0]->setIsComing($isComing);
                        }
                          
                        if($email !=null){
                            $guestSelected[0]->setEmail($email);
                        }
                        if($phoneNumber != null){
                            $guestSelected[0]->setPhoneNumber($phoneNumber);
                        }
                        if($firstname != null){
                            $guestSelected[0]->setFirstname($firstname);
                        }
                        if($lastname != null){
                            $guestSelected[0]->setLastname($lastname);
                        }

                        if($type!=null)
                        {
                           $typeAsked= $TypeRepository->findByName($type);
                           if($typeAsked!=[])
                            {
                                if($typeAsked[0]->getName()=='maried')
                                {
                                    $guestMariedVerify = $GuestRepository->checkNoMoreThan2Maried($CurrentProject[0]->getId(),'maried');
                                    if(count($guestMariedVerify)==2)
                                    {
                                        $loadingDataStatus = 'failure';
                                        $errorStatus ='there is already 2 maried created';
                                        $response = new Response();
                                        $response->setContent(json_encode(
                            
                                            [  
                                                
                                                'loading_data_status'=> $loadingDataStatus,
                                                'error_status' => $errorStatus ,
                                                
                                            ]));
                                        $response->headers->set('Content-Type', 'application/json');
                                                
                                        return $response ;
                                    }
                                }
                                if($typeAsked[0]->getName()!='maried'){
                                    $guest->setType($typeAsked[0]);
                                }
                            }else{

                                $loadingDataStatus = 'failure';
                                $errorStatus ='type not found:'.$type.',available type are:maried,guest,witness';
                                $response = new Response();
                                $response->setContent(json_encode(
                    
                                    [  
                                        
                                        'loading_data_status'=> $loadingDataStatus,
                                        'error_status' => $errorStatus ,
                                        
                                    ]));
                                $response->headers->set('Content-Type', 'application/json');
                                        
                                return $response ;

                            }
                        }
                        
                        $em->persist($guestSelected[0]);
                        $em->flush();
                        
                        $updateGuestStatus = 'success';
                        $errorStatus = '';
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
                                
                                'update_guest_status' => $updateGuestStatus,
                                
                            
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    
                    
                    }else{

                        $updateDataStatus = 'failure';
                        $errorStatus ='Guest not found';
                        $response = new Response();
                        $response->setContent(json_encode(
            
                            [  
                                
                                'update_data_status'=> $updateDataStatus,
                                'error_status' => $errorStatus ,
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    }




                }

            }else{

                $updateDataStatus = 'failure';
                $errorStatus ='missing data (token/username)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'update_data_status'=> $updateDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }


            
        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }




     /**
     * @Route("/api/project/guests/remove", name="remove_guests",  methods={"GET","HEAD", "POST" })
     */

    public function removeGuests(GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {

            $guestId = $request->request->get('id');
            $token = $request->request->get('token');

            $username = $request->request->get('username');

            if($token!=null && $username!=null)
            {       

                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);

                
                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }


                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
                {  
                    
                    $guestSelected = $GuestRepository->findbyDoubleId($guestId,$CurrentProject[0]->getId());
                    if($guestSelected!=[])
                    {   
                        
                        
                        $CurrentProject[0]->removeGuest($guestSelected[0]);
                        $em->remove($guestSelected[0]);
                        $em->persist($CurrentProject[0]);
                        $em->flush();
                        
                        $updateGuestStatus = 'success';
                        $errorStatus = '';
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
                                
                                'update_guest_status' => $updateGuestStatus,
                                
                            
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    
                    
                    }else{

                        $updateDataStatus = 'failure';
                        $errorStatus ='Guest not found';
                        $response = new Response();
                        $response->setContent(json_encode(
            
                            [  
                                
                                'update_data_status'=> $updateDataStatus,
                                'error_status' => $errorStatus ,
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    }




                }

            }else{

                $updateDataStatus = 'failure';
                $errorStatus ='missing data (token/username)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'update_data_status'=> $updateDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }


            
        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }


     /**
     * @Route("/api/project/edit/department", name="edit_department",  methods={"GET","HEAD", "POST" })
     */

    public function updateDepartment(DepartmentRepository $DepartmentRepository, GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {

        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();

        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        { 

            $token = $request->request->get('token');
            $departmentAsked = $request->request->get('newDepartment');

            $username = $request->request->get('username');

            if($token!=null && $username!=null && $departmentAsked!=null)
            {  
                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);

                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }

                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
                {  
                    
                    $departmentSelected = $DepartmentRepository->findbyNumber($departmentAsked);
                    if($departmentSelected!=[])
                    {   
                        
                        
                        $CurrentProject[0]->setDepartment($departmentSelected[0]);
                        $em->persist($CurrentProject[0]);
                        $em->flush();
                        
                        $updateDepartmentStatus = 'success';
                        $errorStatus = '';
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
                                
                                'update_guest_status' => $updateDepartmentStatus,
                                
                            
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    
                    
                    }else{

                        $updateDataStatus = 'failure';
                        $errorStatus ='Department not found';
                        $response = new Response();
                        $response->setContent(json_encode(
            
                            [  
                                
                                'update_data_status'=> $updateDataStatus,
                                'error_status' => $errorStatus ,
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    }




                }



            }else{

                $updateDataStatus = 'failure';
                $errorStatus ='missing data (token/username/newDepartment)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'update_data_status'=> $updateDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }

        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }


     /**
     * @Route("/api/project/newsletter", name="newsletter",  methods={"GET","HEAD", "POST" })
     */

    public function newsletter(\Swift_Mailer $mailer , GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {   

            
            $token = $request->request->get('token');
            $message = $request->request->get('message');

            $username = $request->request->get('username');

            if($token!=null && $username!=null)
            {       

                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);

                
                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }


                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
    
                {   
                    $baseUrl ='http://www.owedding.fr/guest/project/newsletter/parameter/';
                    
                    if($message!=null)
                    {   
                        
                        $CurrentProject[0]->getGuest();
                        foreach($CurrentProject[0]->getGuest() as $guest)
                        {
                            $guestToken = $guest->getToken();
                            $guestName =$guest->getFirstname();
                            $email = $guest->getEmail();
                            $newsletterStatus = $guest->getNewsletterIsActive();
                            if($email!=null && $newsletterStatus ===true)
                            {   
                                
                                $newsletter = (new \Swift_Message('Owedding newsletter'))
                                ->setFrom('O\'wedding@project.com')
                                ->setTo($email)
                                ->setBody(
                                    $this->renderView(
                                        // templates/emails/registration.html.twig
                                        'api/email/newsletterWithMessage.html.twig',
                                        [   
                                            'message' => $message,
                                            'user_email' =>$CurrentUserConnect[0]->getEmail(),
                                            'guest_name'=>$guestName,
                                            'username'=> $CurrentUserConnect[0]->getUsername(),
                                            'project_name' => $CurrentProject[0]->getUser(),
                                            'link'=>$baseUrl.$guestToken
                                        ]
                                    ),
                                    'text/html'
                                );
                        
                                $result = $mailer->send($newsletter);
                            }
                            

                            
                        }
                        $newsletterStatus = 'success';
                        $errorStatus = '';
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
                                
                                'newsletter_status' => $newsletterStatus,
                                
                            
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    
                    
                    }else{

                        $guestList = $CurrentProject[0]->getGuest();
                        foreach($guestList as $guest)
                        {
                            $guestName = $guest->getFirstname();
                            $guestToken = $guest->getToken();
                            $email = $guest->getEmail();
                            $newsletterStatus = $guest->getNewsletterIsActive();
                            if($email!=null && $newsletterStatus ===true)
                            {   
                                
                                $newsletter = (new \Swift_Message('Owedding newsletter'))
                                ->setFrom('O\'wedding@project.com')
                                ->setTo($email)
                                ->setBody(
                                    $this->renderView(
                                        // templates/emails/registration.html.twig
                                        'api/email/newsletterWithoutMessage.html.twig',
                                        [   'user_email' =>$CurrentUserConnect[0]->getEmail(),
                                            'guest_name'=>$guestName,
                                            'username'=> $CurrentUserConnect[0]->getUsername(),
                                            'project_name' => $CurrentProject[0]->getUser(),
                                            'link'=>$baseUrl.$guestToken

                                        ]
                                    ),
                                    'text/html'
                                );
                        
                                $result = $mailer->send($newsletter);
                            }

                        
                        }

                        $newsletterStatus = 'success';
                        $errorStatus = '';
                        $response = new Response();
                        $response->setContent(json_encode(
        
                            [  
                                
                                'newsletter_status' => $newsletterStatus,
                                
                            
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;




                    }

                }


            
            }else{

                $updateDataStatus = 'failure';
                $errorStatus ='missing data (token/username)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'update_data_status'=> $updateDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }

        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }



    }






     /**
     * @Route("/project/newsletter/parameter/{jwt}", name="newsletter_parameter",  methods={"GET","HEAD", "POST" })
     */

    public function newsletterUnsubscribe(\Swift_Mailer $mailer,GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, Request $request,UserPasswordEncoderInterface $encoder, $jwt,EntityManagerInterface $em )
    {

        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        
        


       

            if($jwt!=null)
            {
                $CurrentGuestConnect = $GuestRepository->findByJwt($jwt);
                if($CurrentGuestConnect==[])
                {

                    
                    return $this->render('api/newsletter/newsletterGuestNotFound.html.twig');

                
                }
                

                if($CurrentGuestConnect!=[])
                {
                    if($CurrentGuestConnect[0]->getIsActive()===true)
                    {

                    
                        if($CurrentGuestConnect[0]->getIsComing()===true)
                        {
                            $isComingStatus = 'present';
                        }
                        
                        if($CurrentGuestConnect[0]->getIsComing()===false)
                        {
                            $isComingStatus = 'absent';
                        }

                        if($CurrentGuestConnect[0]->getNewsletterIsActive()===true)
                        {
                            $isNewsletterActive = 's\'abonner';
                        }
                        
                        if($CurrentGuestConnect[0]->getNewsletterIsActive()===false)
                        {
                            $isNewsletterActive = 'se desabonner';
                        }

                       

                        $guestProjectData = [];
                       
                        
                        
                           
                            foreach ($CurrentGuestConnect[0]->getProjects() as $guestProject)
                            {
                                //Create a new DateTime object using the date string above.
                                $dateTime = new \DateTime($guestProject->getDeadline());
                                $type ='maried';
                                $mariedCouple = $GuestRepository->checkNoMoreThan2Maried($guestProject->getId(),$type);
                                $limiteDate = intval($dateTime->format('U')-15768000);
                                $date = DateTime::createFromFormat('U', $limiteDate);
                                
                                if($mariedCouple!=[])
                                { 
                                    if(isset($mariedCouple[0])===true && isset($mariedCouple[1])===true)
                                    {
                                        $guestProjectData[] =
                                        [
                                        
                                            'maried1'=> $mariedCouple[0]->getFirstname(),
                                            'maried2'=> $mariedCouple[1]->getFirstname(),
                                            'user_project'=>$guestProject->getUser()->getUsername(),
                                            'deadline' => $guestProject->getDeadline(),
                                            'before_deadline'=>$date->format('d-m-Y')
                                            
                            
                                        ];
                                    }

                                    if(isset($mariedCouple[0])===true && isset($mariedCouple[1])===false)
                                    {
                                        $guestProjectData[] =
                                        [
                                        
                                            'maried1'=> $mariedCouple[0]->getFirstname(),
                                            'user_project'=>$guestProject->getUser()->getUsername(),
                                            'deadline' => $guestProject->getDeadline(),
                                            'before_deadline'=>$date->format('d-m-Y')
                                            
                                            
                                        ];
                                    }

                                    if(isset($mariedCouple[0])===false && isset($mariedCouple[1])===false)
                                    {
                                        $guestProjectData[] =
                                        [
                                        
                                            
                                            'user_project'=>$guestProject->getUser()->getUsername(),
                                            'deadline' => $guestProject->getDeadline(),
                                            
                                            
                                            
                                        ];
                                    } 
                                   

                                }
                               
                               
    
    
                            }
                        
                        
                       
                       
                       
                        $form = $this->createForm(GuestType::class,$CurrentGuestConnect[0]);
                        $form->handleRequest($request);

                       
                        if ($form->isSubmitted() && $form->isValid()) {
                            
                        
                            

                            $form->get('meat_meal_number')->getData();

                            if($form->get('is_coming_with')->getData()-($form->get('vegetarian_meal_number')->getData()+$form->get('meat_meal_number')->getData())==0)
                            {
                                $CurrentGuestConnect[0]->setIsActive(false);
                                //rajouter mailer
                                // $newsletter = (new \Swift_Message('Owedding newsletter'))
                                // ->setFrom('O\'wedding@project.com')
                                // ->setTo($email)
                                // ->setBody(
                                //     $this->renderView(
                                //         // templates/emails/registration.html.twig
                                //         'api/email/newsletterWithoutMessage.html.twig',
                                //         [   'user_email' =>$CurrentUserConnect[0]->getEmail(),
                                //             'guest_name'=>$guestName,
                                //             'username'=> $CurrentUserConnect[0]->getUsername(),
                                //             'project_name' => $CurrentProject[0]->getUser(),
                                //             'link'=>$baseUrl.$guestToken

                                //         ]
                                //     ),
                                //     'text/html'
                                // );
                        
                                // $result = $mailer->send($newsletter);

                                
                                $em = $this->getDoctrine()->getManager();
                                $em->persist($CurrentGuestConnect[0]);
                                $em->flush();
                                return $this->render('api/newsletter/newsletterResume.html.twig',
                                [ 
        
                                    'coming_status'=> $isComingStatus,
                                    'guest_id' => $CurrentGuestConnect[0]->getId(),
                                    'jwt' =>$CurrentGuestConnect[0]->getToken(),
                                    'newsletter_status' =>$isNewsletterActive ,
                                    'guest' => $CurrentGuestConnect[0],
                                    'user' => $guestProjectData[0],
        
                                ]);
                               
                            }else{

                                if($form->get('is_coming_with')->getData()-($form->get('vegetarian_meal_number')->getData()+$form->get('meat_meal_number')->getData())>0)
                                {
                                    
                                    $this->addFlash(

                                        'warning',
                                        'le nombre de personne venant est supérieur au nombre de plat commandé'
                                    );

                                }

                                if($form->get('is_coming_with')->getData()-($form->get('vegetarian_meal_number')->getData()+$form->get('meat_meal_number')->getData())<0)
                                {
                                    
                                    $this->addFlash(

                                        'warning',
                                        'le nombre de plat commandé est supérieur au nombre total de personne'
                                    );

                                }
                
                            
                            }
                        }
                        return $this->render('api/newsletter/newsletterStatus.html.twig', [
                           
                            'form' => $form->createView(),
                            'coming_status'=> $isComingStatus,
                            'guest_id' => $CurrentGuestConnect[0]->getId(),
                            'jwt' =>$CurrentGuestConnect[0]->getToken(),
                            'newsletter_status' =>$isNewsletterActive ,
                            'guest' => $CurrentGuestConnect[0],
                            'user' => $guestProjectData[0],
                        
                        ]);
                        
                    }else{

                        if($CurrentGuestConnect[0]->getNewsletterIsActive()===true)
                        {
                            $isNewsletterActive = 's\'abonner';
                        }
                        
                        if($CurrentGuestConnect[0]->getNewsletterIsActive()===false)
                        {
                            $isNewsletterActive = 'se desabonner';
                        }

                        $guestProjectData = [];
                       
                        
                        
                           
                        foreach ($CurrentGuestConnect[0]->getProjects() as $guestProject)
                        {
                            //Create a new DateTime object using the date string above.
                            $dateTime = new \DateTime($guestProject->getDeadline());
                            $type ='maried';
                            $mariedCouple = $GuestRepository->checkNoMoreThan2Maried($guestProject->getId(),$type);
                            $limiteDate = intval($dateTime->format('U')-15768000);
                            $date = DateTime::createFromFormat('U', $limiteDate);
                            
                            if($mariedCouple!=[])
                            { 
                                if(isset($mariedCouple[0])===true && isset($mariedCouple[1])===true)
                                {
                                    $guestProjectData[] =
                                    [
                                    
                                        'maried1'=> $mariedCouple[0]->getFirstname(),
                                        'maried2'=> $mariedCouple[1]->getFirstname(),
                                        'user_project'=>$guestProject->getUser()->getUsername(),
                                        'deadline' => $guestProject->getDeadline(),
                                        'before_deadline'=>$date->format('d-m-Y')
                                        
                        
                                    ];
                                }

                                if(isset($mariedCouple[0])===true && isset($mariedCouple[1])===false)
                                {
                                    $guestProjectData[] =
                                    [
                                    
                                        'maried1'=> $mariedCouple[0]->getFirstname(),
                                        'user_project'=>$guestProject->getUser()->getUsername(),
                                        'deadline' => $guestProject->getDeadline(),
                                        'before_deadline'=>$date->format('d-m-Y')
                                        
                                        
                                    ];
                                }

                                if(isset($mariedCouple[0])===false && isset($mariedCouple[1])===false)
                                {
                                    $guestProjectData[] =
                                    [
                                    
                                        
                                        'user_project'=>$guestProject->getUser()->getUsername(),
                                        'deadline' => $guestProject->getDeadline(),
                                        
                                        
                                        
                                    ];
                                } 
                                
                            }
                           
                           


                        }
                    

                        return $this->render('api/newsletter/newsletterResume.html.twig',
                        [ 

                            
                            'guest_id' => $CurrentGuestConnect[0]->getId(),
                            'jwt' =>$CurrentGuestConnect[0]->getToken(),
                            'newsletter_status' =>$isNewsletterActive ,
                            'guest' => $CurrentGuestConnect[0],
                            'user' => $guestProjectData[0],

                        ]);
                    }         
                        
                }
                   
                   

                
            }else{


                $loadingDataStatus = 'failure';
                $errorStatus ='missing data';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'loading_data_status'=> $loadingDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;

               
            }

                
       


            
    }

      /**
     * @Route("/api/project/guest/information/change/{id}/{jwt}/{order}", name="change_guest_status",  methods={"GET","HEAD", "POST" })
     */

    public function changeGuestStatus(GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em ,$order ,$id ,$jwt )
    {   

        
        if($order!=null )
        {
            if($order=='is_coming')
            {
               $currentGuestConnect = $GuestRepository->findById($id);
               $currentGuestConnectJwt = $GuestRepository->findByJwt($jwt);
               if($currentGuestConnect!=[] && $currentGuestConnectJwt[0]->getToken()==$currentGuestConnect[0]->getToken())
                {   
                    if($currentGuestConnect[0]->getIsComing() === true)
                    {

                        $jwt = $currentGuestConnect[0]->getToken();
                        $currentGuestConnect[0]->setIsComing(false);
                        $currentGuestConnect[0]->setIsComingWith(0);
                        $currentGuestConnect[0]->setVegetarianMealNumber(0);
                        $currentGuestConnect[0]->setMeatMealNumber(0);
                        
                        $em->persist($currentGuestConnect[0]);
                        $em->flush();
                        
                        return $this->redirectToRoute('newsletter_parameter', array('jwt' => $jwt));


                    }

                    else{

                        $jwt = $currentGuestConnect[0]->getToken();
                        $currentGuestConnect[0]->setIsComing(true);
                        $em->persist($currentGuestConnect[0]);
                        $em->flush();

                        return $this->redirectToRoute('newsletter_parameter', array('jwt' => $jwt));


                    }
                  
                   
                }

               if($currentGuestConnect==[])
               {

                return $this->render('api/newsletter/newsletterGuestNotFound.html.twig');

               }
            }


            if($order=='newsletter_status')
            {
                $currentGuestConnect = $GuestRepository->findById($id);
                $currentGuestConnectJwt = $GuestRepository->findByJwt($jwt);
                
                if($currentGuestConnect!=[] && $currentGuestConnectJwt[0]->getToken()==$currentGuestConnect[0]->getToken())
                {  

                    if($currentGuestConnect[0]->getNewsletterIsActive() === true)
                    {

                        $jwt = $currentGuestConnect[0]->getToken();
                        $currentGuestConnect[0]->setNewsletterIsActive(false);
                        $em->persist($currentGuestConnect[0]);
                        $em->flush();
                        
                        return $this->redirectToRoute('newsletter_parameter', array('jwt' => $jwt));


                    }


                    else{

                        $jwt = $currentGuestConnect[0]->getToken();
                        $currentGuestConnect[0]->setNewsletterIsActive(true);
                        $em->persist($currentGuestConnect[0]);
                        $em->flush();

                        return $this->redirectToRoute('newsletter_parameter', array('jwt' => $jwt));


                    }



                }

                if($currentGuestConnect==[])
               {

                return $this->render('api/newsletter/newsletterGuestNotFound.html.twig');

               }

            }

            if($order!='newsletter_status' && $order!='is_coming' )
            {   


            }

        }else{

        }
    }

     /**
     * @Route("/api/project/provider/add", name="add_provider",  methods={"GET","HEAD", "POST" })
     */

    public function addProvider(ProviderRepository $ProviderRepository, GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {

            $providerId = $request->request->get('id');
            $token = $request->request->get('token');

            $username = $request->request->get('username');

            if($token!=null && $username!=null)
            {       

                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);

                
                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }


                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)   
                {  

                    $providerExistingTest = $ProviderRepository->findbyId($providerId);
                    if($providerExistingTest!=[])
                    {

                        $providerSelected = $ProviderRepository->findbyDoubleId($providerId,$CurrentProject[0]->getId());
                        if($providerSelected!=[])
                        {   
                           

                            $updateDataStatus = 'failure';
                            $errorStatus ='Provider added already';
                            $response = new Response();
                            $response->setContent(json_encode(
                
                                [  
                                    
                                    'update_data_status'=> $updateDataStatus,
                                    'error_status' => $errorStatus ,
                                    
                                ]));
                            $response->headers->set('Content-Type', 'application/json');
                                    
                            return $response ;
                        
                              
                                
                        }else{

                            $CurrentProject[0]->addProvider($providerExistingTest[0]);
                            $CurrentProject[0]->setCurrentBudget($CurrentProject[0]->getCurrentBudget()+$providerExistingTest[0]->getAveragePrice());
                            $em->persist($CurrentProject[0]);
                            $em->flush($CurrentProject[0]);
                            $updateDataStatus = 'success';
                            $errorStatus ='';
                            $response = new Response();
                            $response->setContent(json_encode(
                
                                [  
                                    
                                    'update_data_status'=> $updateDataStatus,
                                    
                                    
                                ]));
                            $response->headers->set('Content-Type', 'application/json');
                                    
                            return $response ;
                        }     
                        
                        

                       
                       
                       
                    
                    
                    }else{

                        $updateDataStatus = 'failure';
                        $errorStatus ='Provider not found';
                        $response = new Response();
                        $response->setContent(json_encode(
            
                            [  
                                
                                'update_data_status'=> $updateDataStatus,
                                'error_status' => $errorStatus ,
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    }




                }

            }else{

                $updateDataStatus = 'failure';
                $errorStatus ='missing data (token/username)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'update_data_status'=> $updateDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }


            
        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
       
        
    }



     /**
     * @Route("/api/project/provider/remove", name="remove_provider",  methods={"GET","HEAD", "POST" })
     */

    public function removeProvider(ProviderRepository $ProviderRepository, GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {

            $providerId = $request->request->get('id');
            $token = $request->request->get('token');

            $username = $request->request->get('username');

            if($token!=null && $username!=null)
            {       

                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);

                
                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }


                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

              
                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
    
                {  

                    $providerExistingTest = $ProviderRepository->findbyId($providerId);
                    if($providerExistingTest!=[])
                    {

                        $providerSelected = $ProviderRepository->findbyDoubleId($providerId,$CurrentProject[0]->getId());
                        if($providerSelected!=[])
                        {   
                           

                            $CurrentProject[0]->removeProvider($providerExistingTest[0]);
                            $CurrentProject[0]->setCurrentBudget($CurrentProject[0]->getCurrentBudget()-$providerExistingTest[0]->getAveragePrice());
                            $em->persist($CurrentProject[0]);
                            $em->flush($CurrentProject[0]);
                            $updateDataStatus = 'success';
                            $errorStatus ='';
                            $response = new Response();
                            $response->setContent(json_encode(
                
                                [  
                                    
                                    'update_data_status'=> $updateDataStatus,
                                    
                                    
                                ]));
                            $response->headers->set('Content-Type', 'application/json');
                                    
                            return $response ;
                        
                              
                                
                        }else{

                           

                            $updateDataStatus = 'failure';
                            $errorStatus ='This provider does not belong to this project';
                            $response = new Response();
                            $response->setContent(json_encode(
                
                                [  
                                    
                                    'update_data_status'=> $updateDataStatus,
                                    'error_status' => $errorStatus ,
                                    
                                ]));
                            $response->headers->set('Content-Type', 'application/json');
                                    
                            return $response ;
                        }     
                        
                        

                       
                       
                       
                    
                    
                    }else{

                        $updateDataStatus = 'failure';
                        $errorStatus ='Provider not found';
                        $response = new Response();
                        $response->setContent(json_encode(
            
                            [  
                                
                                'update_data_status'=> $updateDataStatus,
                                'error_status' => $errorStatus ,
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                
                        return $response ;
                    }




                }

            }else{

                $updateDataStatus = 'failure';
                $errorStatus ='missing data (token/username)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
                        
                        'update_data_status'=> $updateDataStatus,
                        'error_status' => $errorStatus ,
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            }


            
        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    }


     /**
     * @Route("/api/search/provider", name="search_bar",  methods={"GET","HEAD", "POST" })
     */

    public function searchbar(ProviderRepository $ProviderRepository, GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {
        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {
            
            $token = $request->request->get('token');
            $departmentAsked = $request->request->get('department');
            $priceAsked = $request->request->get('price');
            $themeAsked = $request->request->get('theme');
            $username = $request->request->get('username');

            if($token!=null && $username!=null)
            {       

                $CurrentUserConnect = $UserRepository->findByJwt($token);
            

                $CurrentProject = $projectRepository->findByJwt($token);

                if($CurrentUserConnect==[] && $CurrentProject==[])
                {

                    
                    $loadDataStatus = 'failed';
                    $errorStatus = 'unknown JWT please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'load_data_status' => $loadDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;

                
                }


                if($CurrentUserConnect!=[] && $CurrentProject==[] )
                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'a User without project has been found' ;
                    $response = new Response();
                    $response->setContent(json_encode(

                        [  

                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[] && $CurrentUserConnect[0]->getSessionDuration()< $CurrentTimestamp)

                {
                    $updateDataStatus = 'failed';
                    $errorStatus = 'Session expired please reconnect' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
    
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                            
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
    
                }
    
                

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() != $username)
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'A user attempt to access to the the wrong project data' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }

                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()!= $username )
                {   
    
                    $updateDataStatus = 'failed';
                    $errorStatus = 'The username given does not match with the token' ;
                    $response = new Response();
                    $response->setContent(json_encode(
    
                        [  
                            
                            'update_data_status' => $updateDataStatus,
                            'error_status' => $errorStatus ,
                        
                        ]));
                    $response->headers->set('Content-Type', 'application/json');
                            
                    return $response ;
                   
                }


                if($CurrentUserConnect!=[] && $CurrentProject!=[]  &&  $CurrentUserConnect[0]->getSessionDuration()> $CurrentTimestamp  && $CurrentUserConnect[0]->getUsername()== $username && $CurrentProject[0]->getUser()->getUsername() == $username)
    
                { 
                   

                    $searchResult = $ProviderRepository->findByPTDCriteria($priceAsked,$themeAsked,$departmentAsked);
                    // DD($searchResult);
                    if($searchResult ==[])
                    {
                        $searchDataStatus = 'success';
                        $searchStatus = 'provider not found' ;
                        $response = new Response();
                        $response->setContent(json_encode(
            
                            [  
                                    
                                'update_data_status' => $searchDataStatus,
                                'error_status' => $searchStatus ,
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                    
                        return $response ;

                    }

                    if($searchResult!=[])
                    {
                       
                        $searchData=[];
                        foreach($searchResult as $currentResult )
                        {
                            foreach($currentResult->getDepartment() as $CurrentResultDepartement)
                            {
                               
                                     $CurrentResultDepartement->getName();
                                    $CurrentResultDepartement->getNumber();
                                
                               
                            }
                            foreach($currentResult->getTheme() as $CurrentResultTheme)
                            {
                               
                                    $CurrentResultTheme->getName();
                                    
                                
                               
                            }

                            $searchData[] = 
                                [ 
                                    'id' =>$currentResult->getId(),
                                    'name'=> $currentResult->getName(),
                                    'email' => $currentResult->getEmail(),
                                    'phone_number' => $currentResult->getPhoneNumber(),                                   
                                    'average_price' => $currentResult->getAveragePrice(),
                                    'provider_department_name' => $CurrentResultDepartement->getName(),
                                    'provider_department_number'=>$CurrentResultDepartement->getNumber(),
                                    'provider_theme_name' =>$CurrentResultTheme->getName(),
                                    'provider_description'=> $currentResult->getDescription(),
                                    'provider_image' => $currentResult->getPicture(),

                                ];
                               

                        }
                        $resultNumber = count($searchResult);
                        $response = new Response();
                        $searchDataStatus = 'success';
                        $searchStatus = 'provider found' ;
                        $response->setContent(json_encode(
            
                            [   
                                'search_data_status' => $searchDataStatus,
                                'search_status' => $searchStatus ,
                                'search_result' => $searchData,
                                'search_result_number' =>$resultNumber
                                
                                
                                
                            ]));
                        $response->headers->set('Content-Type', 'application/json');
                                    
                        return $response ;

                    }
                    


                }






            }else{

                $sendMessageStatus ='failure';
                $errorStatus = 'missing data(username/token)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
    
                        
    
                        'send_message_status' =>$sendMessageStatus,
                        
                        'error_status'=>$errorStatus
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            
            }

        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }
    
    }



      /**
     * @Route("/api/contact/admin", name="contact_admin",  methods={"GET","HEAD", "POST" })
     */

    public function adminContact(\Swift_Mailer $mailer , GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {

        $date = new DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )
        {

            $messageToAdmin = $request->request->get('message');
            $CurrentName = $request->request->get('name');
            $CurrentEmail = $request->request->get('email');

            if($messageToAdmin!=null && $CurrentName!=null && $CurrentEmail!=null)
            {

                
                $message1 = (new \Swift_Message('Contact administrateur O\'wedding'))
                ->setFrom('O\'wedding@project.fr')
                ->setTo('oweddingproject@gmail.com')
                ->setBody(
                    $this->renderView(
                        
                        'api/email/messageToAdmin.html.twig',
                        ['messageToAdmin' => $messageToAdmin,
                        'name'=>$CurrentName,
                        'email'=>$CurrentEmail]
                    ),
                    'text/html'
                );
                
                $result = $mailer->send($message1);

                $message2 = (new \Swift_Message('Contact administrateur O\'wedding'))
                ->setFrom('O\'wedding@project.fr')
                ->setTo($CurrentEmail)
                ->setBody(
                    $this->renderView(

                        
                        'api/email/messageToConfirmAdminContact.html.twig',
                        ['messageToAdmin' => $messageToAdmin,
                        'name'=>$CurrentName,
                        ]
                    ),
                    'text/html'
                );
                
                $result = $mailer->send($message2);


                $sendMessageStatus ='success';
                $errorStatus = '';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
    
                        
    
                        'send_message_status' =>$sendMessageStatus,
                        
                        
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;

            }else{

                $sendMessageStatus ='failure';
                $errorStatus = 'missing data(message/name/email)';
                $response = new Response();
                $response->setContent(json_encode(
    
                    [  
    
                        
    
                        'send_message_status' =>$sendMessageStatus,
                        
                        'error_status'=>$errorStatus
                    ]));
                $response->headers->set('Content-Type', 'application/json');
                        
                return $response ;
            
            }

        }else{

            $response = new Response();
            $response->setContent(json_encode(

                [  

                    

                    'connection_API_status' =>'refused',
                    
                    'Header_token'=>"invalide"
                ]));
            $response->headers->set('Content-Type', 'application/json');
                    
            return $response ;
        }

    }


      /**
     * @Route("/api/test/trafic", name="trafic_view",  methods={"GET","HEAD", "POST" })
     */

    public function traficView(\Swift_Mailer $mailer , GuestRepository $GuestRepository , UserRepository $UserRepository,ProjectRepository $projectRepository, ?Request $request=null,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em )
    {   

        $status = true ;
        $date = new \DateTime();
        $CurrentTimestamp = $date->getTimestamp();
        $connectedUserList = $UserRepository->findConnectedNumber($status ,$CurrentTimestamp);
        $number = count($connectedUserList);
        $response = new Response();
        $response->setContent(json_encode(

            [  

                

                "connected_number" =>  $number 
            ]));
        $response->headers->set('Content-Type', 'application/json');
                
        return $response ;


    }


     /**
     * @Route("/api/mercure/sendtext" , name="mercure_test", methods={"GET","HEAD", "POST" })
     */

    public function mercureTest(Publisher $publisher,Request $request)
    {   


        
        $date = new \DateTime();
        $DateFormat=$date->format('d-m-Y');
        $currentDate=explode("-" , $DateFormat);
        $day=$currentDate[0];
        $month=$currentDate[1];
        $year=$currentDate[2];
        $DateFormat2 = $date->format('d-m-Y-H-i-s');
        $currentDate2=explode( "-", $DateFormat2);
        $hour=$currentDate2[3];
        $minute =$currentDate2[4];
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $message = $request->request->get('message');        
        $data=json_encode(
        
            [  

                
                "remove"=>true,
                "name" =>  $name,
                "message" =>$message,
                "day" => $DateFormat,
                "hour"=> $hour.'h'.$minute
            ]);
        $update = new Update('http://monsite.com/ping/'.$id , $data);   
        $id2 = $publisher($update);
        
        
        return $this->redirectToRoute('mercure',['id'=>$id]);

    }

     /**
     * @Route("/api/mercure/before_sendtext/add" , name="mercure_before_send_add", methods={"GET","HEAD", "POST" })
     */

    public function mercureTest2(Publisher $publisher,Request $request)
    {
        $date = new \DateTime();
        $DateFormat=$date->format('d-m-Y');
        $currentDate=explode("-" , $DateFormat);
        $day=$currentDate[0];
        $month=$currentDate[1];
        $year=$currentDate[2];
        $DateFormat2 = $date->format('d-m-Y-H-i-s');
        $currentDate2=explode( "-", $DateFormat2);
        $hour=$currentDate2[3];
        $minute =$currentDate2[4];
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $message = $request->request->get('message');
        $data=json_encode(

            [  

                

                "name" =>  $name,
               
            ]);
        $update = new Update('http://monsite.com/ping/'.$id , $data);   
        $id2 = $publisher($update);
        
        
        return $this->redirectToRoute('mercure',['id'=>$id]);

    }

    /**
     * @Route("/api/mercure/before_sendtext/remove" , name="mercure_before_sendtext_remove", methods={"GET","HEAD", "POST" })
     */

    public function mercureTest3(Publisher $publisher,Request $request)
    {   
        $date = new \DateTime();
        $DateFormat=$date->format('d-m-Y');
        $currentDate=explode("-" , $DateFormat);
        $day=$currentDate[0];
        $month=$currentDate[1];
        $year=$currentDate[2];
        $DateFormat2 = $date->format('d-m-Y-H-i-s');
        $currentDate2=explode( "-", $DateFormat2);
        $hour=$currentDate2[3];
        $minute =$currentDate2[4];
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $message = $request->request->get('message');
        $data=json_encode(

            [  

                
                "remove"=>true,
                "name" =>  $name,
               
            ]);
        $update = new Update('http://monsite.com/ping/'.$id , $data);   
        $id2 = $publisher($update);
        
        
        return $this->redirectToRoute('mercure',['id'=>$id]);

    }
    
      /**
     * @Route("/mercure/chat/{id}" , name="mercure", methods={"GET","HEAD", "POST" })
     */

    public function mercurePublic(Publisher $publisher,$id)
    {
       
       
        
        return $this->render('api/mercure/mercure.html.twig',['id'=>$id]);
       
        
      
        

    }

     /**
     * @Route("/api/mercure/expose/joiner" , name="mercure_private_chat", methods={"GET","HEAD", "POST" })
     */

    public function mercurePrivateChat(Publisher $publisher,Request $request)
    {
        
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $message = $request->request->get('message');        
        $data=json_encode(
        
            [  

                
                
                "name" =>  $name,
                "message" =>$message,
                "joined"=>true
            ]);
        $update = new Update('http://monsite.com/ping/'.$id , $data);   
        $id2 = $publisher($update);
        
        
        return $this->redirectToRoute('mercure',['id'=>$id]);

        
        
        


    }

      /**
     * @Route("/api/mercure/expose/anonymous" , name="mercure_anonymous", methods={"GET","HEAD", "POST" })
     */

    public function mercureAnonymous(Publisher $publisher,Request $request)
    {
        
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $message = $request->request->get('message');        
        $data=json_encode(
        
            [  

                
                
                "name" =>  $name,
                "message" =>$message,
                "anonymous"=>true
            ]);
        $update = new Update('http://monsite.com/ping/'.$id , $data);   
        $id2 = $publisher($update);
        
        
        return $this->redirectToRoute('mercure',['id'=>$id]);

        
        
        


    }

}
