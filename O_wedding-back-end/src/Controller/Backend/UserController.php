<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEditType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/backend/user", name="backend_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/list", name="list", methods="GET")
     */
    public function list(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('backend/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /** Routes pour les tris */

   /**
     * @Route("/list/username/ASC", name="orderby_username_asc")
     */
    public function orderByUsernameASC(UserRepository $userRepository)
    {
        $users = $userRepository->orderByUsernameASC();
       

        return $this->render('backend/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/list/username/DESC", name="orderby_username_desc")
     */
    public function orderByUsernameDESC(UserRepository $userRepository)
    {
        $users = $userRepository->orderByUsernameDESC();
       

        return $this->render('backend/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/list/date/ASC", name="orderby_date_asc")
     */
    public function orderByDateASC(UserRepository $userRepository)
    {
        $users = $userRepository->orderByDateASC();
       

        return $this->render('backend/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/list/date/DESC", name="orderby_date_desc")
     */
    public function orderByDateDESC(UserRepository $userRepository)
    {
        $users = $userRepository->orderByDateDESC();
       

        return $this->render('backend/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    

    /**
     * @Route("/{id}", name="show", requirements={"id": "\d+"}, methods="GET")
     */
    public function show(User $user)
    {
        return $this->render('backend/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
    
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );

            return $this->redirectToRoute('backend_user_list');
        }

        return $this->render('backend/user/_form_new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"}, requirements={"id": "\d+"})
     */
    public function edit(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(UserEditType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Profil modifié.');

            return $this->redirectToRoute('backend_user_show', ['id' => $user->getId(),] );
        }

        return $this->render('backend/user/_form_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/password/'id}", name="edit_password", methods={"GET","POST"}, requirements={"id": "\d+"} )
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();

        $form = $this->createForm(UserPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifié.');

            return $this->redirectToRoute('backend_user_show', ['id' => $user->getId(),] );
        }

        return $this->render('backend/user/_form_edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
    * @Route("/delete/{id}", name="delete", methods={"DELETE", "GET"})
    */
   public function delete(Request $request, User $user)
   {
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->remove($user);
           $entityManager->flush();

           $this->addFlash('success', 'L\'utilisateur à bien été supprimé');
       
       return $this->redirectToRoute('backend_user_list');
   }

}
