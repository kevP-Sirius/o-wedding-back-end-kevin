<?php

namespace App\Controller\Backend;

use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backend/department", name="backend_department_")
 */
class DepartmentController extends AbstractController
{
    /**
     * @Route("/list", name="list", methods="GET")
     */
    public function list(DepartmentRepository $departmentRepository)
    {
        $departments = $departmentRepository->findAll();

        return $this->render('backend/department/index.html.twig', [
            'departments' => $departments,
        ]);
    }

    /** Routes pour les tris */

   /**
     * @Route("/list/name/ASC", name="orderby_name_asc")
     */
    public function orderByNameASC(DepartmentRepository $departmentRepository)
    {
        $departments = $departmentRepository->orderByNameASC();
       

        return $this->render('backend/department/index.html.twig', [
            'departments' => $departments,
        ]);
    }

    /**
     * @Route("/list/name/DESC", name="orderby_name_desc")
     */
    public function orderByNameDESC(DepartmentRepository $departmentRepository)
    {
        $departments = $departmentRepository->orderByNameDESC();
       

        return $this->render('backend/department/index.html.twig', [
            'departments' => $departments,
        ]);
    }

    /**
     * @Route("/list/number/ASC", name="orderby_number_asc")
     */
    public function orderByNumberASC(DepartmentRepository $departmentRepository)
    {
        $departments = $departmentRepository->orderByNumberASC();
       

        return $this->render('backend/department/index.html.twig', [
            'departments' => $departments,
        ]);
    }

    /**
     * @Route("/list/number/DESC", name="orderby_number_desc")
     */
    public function orderByNumberDESC(DepartmentRepository $departmentRepository)
    {
        $departments = $departmentRepository->orderByNumberDESC();
       

        return $this->render('backend/department/index.html.twig', [
            'departments' => $departments,
        ]);
    }



    /**
     * @Route("/{id}", name="show", requirements={"id": "\d+"}, methods="GET")
     */
    public function show(Department $department)
    {
       
        return $this->render('backend/department/show.html.twig', [
            'department' => $department,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        $department = new Department();
    
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );

            return $this->redirectToRoute('backend_department_list');
        }

        return $this->render('backend/department/_form_new.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Department $department): Response
    {

        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'info',
                'Mise à jour effectuée'
            );

            return $this->redirectToRoute('backend_department_list');
        }

        return $this->render('backend/department/_form_edit.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }


    /**
    * @Route("/delete/{id}", name="delete", methods={"DELETE", "GET"})
    */
   public function delete(Request $request, Department $department)
   {
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->remove($department);
           $entityManager->flush();

           $this->addFlash('success', 'Le departement à bien été supprimé');
       
       return $this->redirectToRoute('backend_department_list');
   }
}
