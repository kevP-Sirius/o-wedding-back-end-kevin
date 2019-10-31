<?php

namespace App\Controller\Backend;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/backend/project", name="backend_project_")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/list", name="list", methods="GET")
     */
    public function list(ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->findAll();

        return $this->render('backend/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /** Routes pour les tris */

   /**
     * @Route("/list/name/ASC", name="orderby_name_asc", methods="GET")
     */
    public function orderByNameASC(ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->orderByNameASC();
       

        return $this->render('backend/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/list/name/DESC", name="orderby_name_desc", methods="GET" )
     */
    public function orderByNameDESC(ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->orderByNameDESC();
       

        return $this->render('backend/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/list/date/ASC", name="orderby_date_asc", methods="GET")
     */
    public function orderByDateASC(ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->orderByDateASC();
       

        return $this->render('backend/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/list/date/DESC", name="orderby_date_desc", methods="GET")
     */
    public function orderByDateDESC(ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->orderByDateDESC();
       

        return $this->render('backend/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/list/user/ASC", name="orderby_user_asc", methods="GET")
     */
    public function orderByUserASC(ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->orderByUserASC();
       

        return $this->render('backend/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/list/user/DESC", name="orderby_user_desc", methods="GET")
     */
    public function orderByUserDESC(ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->orderByUserDESC();
       

        return $this->render('backend/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }


    /**
     * @Route("/{id}", name="show", methods="GET")
     */
    public function show(Project $project)
    {
       
        return $this->render('backend/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
    * @Route("/delete/{id}", name="delete", methods={"DELETE", "GET"})
    */
   public function delete(Request $request, Project $project)
   {
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->remove($project);
           $entityManager->flush();

           $this->addFlash('success', 'Le projet à bien été supprimé');
       
       return $this->redirectToRoute('backend_project_list');
   }

}
