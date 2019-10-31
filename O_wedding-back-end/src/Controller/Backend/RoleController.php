<?php

namespace App\Controller\Backend;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backend/role", name="backend_role_")
 */
class RoleController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function list(RoleRepository $roleRepository)
    {
        $roles = $roleRepository->findAll();

        return $this->render('backend/role/index.html.twig', [
            'roles' => $roles,
        ]);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id": "\d+"}, methods="GET")
     */
    public function show(Role $role)
    {
       
        return $this->render('backend/role/show.html.twig', [
            'role' => $role,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        $role = new Role();
    
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );

            return $this->redirectToRoute('backend_role_list');
        }

        return $this->render('backend/role/_form_new.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Role $role): Response
    {

        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'info',
                'Mise à jour effectuée'
            );

            return $this->redirectToRoute('backend_role_list');
        }

        return $this->render('backend/role/_form_edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
    * @Route("/delete/{id}", name="delete", methods={"DELETE", "GET"})
    */
   public function delete(Request $request, Role $role)
   {
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->remove($role);
           $entityManager->flush();

           $this->addFlash('success', 'Le role à bien été supprimé');
       
       return $this->redirectToRoute('backend_role_list');
   }
}
