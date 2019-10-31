<?php

namespace App\Controller\Backend;

use App\Entity\Provider;
use App\Form\ProviderType;
use App\Repository\ProviderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backend/provider", name="backend_provider_")
 */
class ProviderController extends AbstractController
{
    /**
     * @Route("/list", name="list", methods="GET")
     */
    public function list(ProviderRepository $providerRepository)
    {
        $providers = $providerRepository->findAll();

        return $this->render('backend/provider/index.html.twig', [
            'providers' => $providers,
        ]);
    }

    /** Routes pour les tris */

   /**
     * @Route("/list/name/ASC", name="orderby_name_asc")
     */
    public function orderByNameASC(ProviderRepository $providerRepository)
    {
        $providers = $providerRepository->orderByNameASC();
       

        return $this->render('backend/provider/index.html.twig', [
            'providers' => $providers,
        ]);
    }

    /**
     * @Route("/list/name/DESC", name="orderby_name_desc")
     */
    public function orderByNameDESC(ProviderRepository $providerRepository)
    {
        $providers = $providerRepository->orderByNameDESC();
       

        return $this->render('backend/provider/index.html.twig', [
            'providers' => $providers,
        ]);
    }

    /**
     * @Route("/list/date/ASC", name="orderby_date_asc")
     */
    public function orderByDateASC(ProviderRepository $providerRepository)
    {
        $providers = $providerRepository->orderByDateASC();
       

        return $this->render('backend/provider/index.html.twig', [
            'providers' => $providers,
        ]);
    }

    /**
     * @Route("/list/provider/DESC", name="orderby_date_desc")
     */
    public function orderByDateDESC(ProviderRepository $providerRepository)
    {
        $providers = $providerRepository->orderByDateDESC();
       

        return $this->render('backend/provider/index.html.twig', [
            'providers' => $providers,
        ]);
    }



    /**
     * @Route("/{id}", name="show", requirements={"id": "\d+"}, methods="GET")
     */
    public function show(Provider $provider)
    {
       
        return $this->render('backend/provider/show.html.twig', [
            'provider' => $provider,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        $provider = new Provider();
    
        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($provider);
            $em->flush();

            $this->addFlash(
                'success',
                'Enregistrement effectué'
            );

            return $this->redirectToRoute('backend_provider_list');
        }

        return $this->render('backend/provider/_form_new.html.twig', [
            'provider' => $provider,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Provider $provider): Response
    {

        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'info',
                'Mise à jour effectuée'
            );

            return $this->redirectToRoute('backend_provider_list');
        }

        return $this->render('backend/provider/_form_edit.html.twig', [
            'provider' => $provider,
            'form' => $form->createView(),
        ]);
    }

    /**
    * @Route("/delete/{id}", name="delete", methods={"DELETE", "GET"})
    */
   public function delete(Request $request, Provider $provider)
   {
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->remove($provider);
           $entityManager->flush();

           $this->addFlash('success', 'Le prestataire à bien été supprimé');
       
       return $this->redirectToRoute('backend_provider_list');
   }


}
