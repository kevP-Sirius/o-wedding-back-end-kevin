<?php

namespace App\Controller;

use App\Form\GuestType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GuestController extends AbstractController
{
    /**
     * @Route("/guest", name="guest")
     */
    public function index()
    {
        $form = $this->createForm(GuestType::class);
        return $this->render('api/newsletter/newsletterStatus.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
