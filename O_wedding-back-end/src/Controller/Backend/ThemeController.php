<?php

namespace App\Controller\Backend;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backend/theme", name="backend_theme_")
 */
class ThemeController extends AbstractController
{
    /**
     * @Route("/{id}", name="show", requirements={"id": "\d+"}, methods="GET")
     */
    public function show(Theme $theme)
    {
       
        return $this->render('backend/theme/show.html.twig', [
            'theme' => $theme,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Theme $theme): Response
    {

        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'info',
                'Mise à jour effectuée'
            );

            return $this->redirectToRoute('backend_theme_list');
        }

        return $this->render('backend/theme/_form_edit.html.twig', [
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }

    /**
    * @Route("/delete/{id}", name="delete", methods={"DELETE", "GET"})
    */
   public function delete(Request $request, Theme $theme)
   {
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->remove($theme);
           $entityManager->flush();

           $this->addFlash('success', 'Le theme à bien été supprimé');
       
       return $this->redirectToRoute('backend_theme_list');
   }



    /**
     * @Route("/list", name="list", methods="GET")
     */
    public function list(ThemeRepository $themeRepository)
    {
        $themes = $themeRepository->findAll();

        return $this->render('backend/theme/index.html.twig', [
            'themes' => $themes,
        ]);
    }

    /** Routes pour les tris */

   /**
     * @Route("/list/name/ASC", name="orderby_name_asc", methods="GET")
     */
    public function orderByNameASC(ThemeRepository $themeRepository)
    {
        $themes = $themeRepository->orderByNameASC();
       

        return $this->render('backend/theme/index.html.twig', [
            'themes' => $themes,
        ]);
    }

     /**
     * @Route("/list/name/DESC", name="orderby_name_desc")
     */
    public function orderByNameDESC(ThemeRepository $themeRepository)
    {
        $themes = $themeRepository->orderByNameDESC();
       

        return $this->render('backend/theme/index.html.twig', [
            'themes' => $themes,
        ]);
    }

    /**
     * @Route("/list/date/ASC", name="orderby_date_asc")
     */
    public function orderByDateASC(ThemeRepository $themeRepository)
    {
        $themes = $themeRepository->orderByDateASC();
       

        return $this->render('backend/theme/index.html.twig', [
            'themes' => $themes,
        ]);
    }

    /**
     * @Route("/list/date/DESC", name="orderby_date_desc")
     */
    public function orderByDateDESC(ThemeRepository $themeRepository)
    {
        $themes = $themeRepository->orderByDateDESC();
       

        return $this->render('backend/theme/index.html.twig', [
            'themes' => $themes,
        ]);
    }


    

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        $theme = new Theme();
    
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($theme);
            $em->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );

            return $this->redirectToRoute('backend_theme_list');
        }

        return $this->render('backend/theme/_form_new.html.twig', [
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }
}
